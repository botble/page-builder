<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Design page ":name"', ['name' => $page->name]) }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/css/toastr.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/css/grapes.min.css?v0.14.23') }}">
    <link rel="stylesheet"
          href="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/css/grapesjs-preset-webpage.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/css/tooltip.css') }}">

    <link rel='stylesheet' href='//fonts.googleapis.com/css?family=Roboto:100%2C100italic%2C300%2C300italic%2C400%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|Roboto+Slab:100%2C300%2C400%2C700&#038;subset=greek-ext%2Cgreek%2Ccyrillic-ext%2Clatin-ext%2Clatin%2Cvietnamese%2Ccyrillic' type='text/css' media='all' />

    <style type="text/css">
        body, html {
            height      : 100%;
            margin      : 0;
            overflow    : hidden;
            font-family : Roboto, SansSerif, sans-serif;
        }
        .button-option-save, .button-option-cancel {
            font-size : 14px;
        }

        .button-option-save:before, .button-option-cancel:before {
            display: inline-block;
            font: normal normal normal 14px/1 FontAwesome;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding-right : 5px;
            font-size : 16px;
        }

        .button-option-save:before {
            content: "\f058";
        }

        .button-option-cancel:before {
            content: "\f137";
        }

        #toast-container .toast-message {
            font-size : 13px;
        }

        #toast-container > .toast-error {
            background-size: 20px;
        }

        #toast-container > .toast-success {
            background-size: 20px;
        }

        iframe {
            user-select         : none;
            -webkit-user-select : none;
        }
    </style>
</head>
<body>
<div id="gjs">
    {!! $page->content !!}
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<script src="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/js/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/js/grapes.min.js') }}"></script>
<script src="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/js/grapesjs-preset-webpage.min.js') }}"></script>
@foreach(config('core.base.general.editor.ckeditor.js', []) as $js)
{!! Html::script($js) !!}
@endforeach
<script src="{{ asset('vendor/core/plugins/page-builder/libraries/grapesjs/js/grapesjs-plugin-ckeditor.min.js') }}"></script>

<script type="text/javascript">
    var plp = '//placehold.it/350x250/';
    var images = [
        plp + '78c5d6/fff/image1.jpg', plp + '459ba8/fff/image2.jpg', plp + '79c267/fff/image3.jpg',
        plp + 'c5d647/fff/image4.jpg', plp + 'f28c33/fff/image5.jpg', plp + 'e868a2/fff/image6.jpg', plp + 'cc4360/fff/image7.jpg',
    ];
    var first_load = true;

    var editor = grapesjs.init({

        height: '100%',
        container: '#gjs',
        fromElement: 1,
        showOffsets: 1,
        canvas: {
            styles: [
                @foreach(Theme::asset()->getAssets('style') as $css)
                    "{{ asset($css) }}",
                @endforeach
            ],
            scripts: [
                @foreach(Theme::asset()->container('header')->getAssets('script') as $header_js)
                    "{{ asset($header_js) }}",
                @endforeach

                @foreach(Theme::asset()->container('footer')->getAssets('script') as $footer_js)
                    "{{ asset($footer_js) }}",
                @endforeach
            ]
        },
        commands: {
            defaults: [
                {
                    id: 'save-page',
                    run: function (editor, senderBtn) {
                        if (first_load) {
                            first_load = false;
                        } else {
                            var html = editor.getHtml();

                            var css = '<style type="text/css">' + editor.getCss() + '</style>';

                            var js = '<script type="text/javascript">' + editor.getJs() + '<\/script>';

                            var content = html + css + js;

                            $.ajax({
                                url: "{{ route('page_builder.save-design', $page->id) }}",
                                type: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    content: content
                                },
                                success: function (data) {
                                    toastr.success(data['message']);
                                },
                                error: function (data) {
                                    console.warn(data['message']);
                                }
                            });
                        }
                        senderBtn.set('active', false);
                    },
                },
                {
                    id: 'back-to-edit-page',
                    run: function () {
                        window.location.href = '{{ route('pages.edit', $page->id) }}';
                    }
                }
            ]
        },
        assetManager: {
            embedAsBase64: 0,
            assets: images,
            upload: '{{ route('media.files.upload') }}',

            // The name used in POST to pass uploaded files
            uploadName: 'file',

            // Custom headers to pass with the upload request
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            // Custom parameters to pass with the upload request, eg. csrf token
            params: {},
        },
        styleManager: {clearProperties: 1},
        plugins: ['gjs-preset-webpage', 'gjs-plugin-ckeditor'],
        pluginsOpts: {
            'gjs-plugin-ckeditor': {
                options: {
                    toolbar: [[
                        'mode',
                        'Source',
                        'Image',
                        'TextColor',
                        'BGColor',
                        'Styles',
                        'Format',
                        'Font',
                        'FontSize',
                        'CreateDiv',
                        'PageBreak',
                        'Bold',
                        'Italic',
                        'Underline',
                        'Strike',
                        'Subscript',
                        'Superscript',
                        'RemoveFormat',
                    ]]
                }
            },
            'gjs-preset-webpage': {
                modalImportTitle: 'Import Template',
                modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                modalImportContent: function (editor) {
                    return editor.getHtml() + '<style>' + editor.getCss() + '</style>'
                },
                aviaryOpts: false,
                blocksBasicOpts: {flexGrid: 1},
                customStyleManager: [{
                    name: 'General',
                    buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
                    properties: [{
                        name: 'Alignment',
                        property: 'float',
                        type: 'radio',
                        defaults: 'none',
                        list: [
                            {value: 'none', className: 'fa fa-times'},
                            {value: 'left', className: 'fa fa-align-left'},
                            {value: 'right', className: 'fa fa-align-right'}
                        ],
                    },
                        {property: 'position', type: 'select'}
                    ],
                }, {
                    name: 'Dimension',
                    open: false,
                    buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                    properties: [{
                        id: 'flex-width',
                        type: 'integer',
                        name: 'Width',
                        units: ['px', '%'],
                        property: 'flex-basis',
                        toRequire: 1,
                    }, {
                        property: 'margin',
                        properties: [
                            {name: 'Top', property: 'margin-top'},
                            {name: 'Right', property: 'margin-right'},
                            {name: 'Bottom', property: 'margin-bottom'},
                            {name: 'Left', property: 'margin-left'}
                        ],
                    }, {
                        property: 'padding',
                        properties: [
                            {name: 'Top', property: 'padding-top'},
                            {name: 'Right', property: 'padding-right'},
                            {name: 'Bottom', property: 'padding-bottom'},
                            {name: 'Left', property: 'padding-left'}
                        ],
                    }],
                }, {
                    name: 'Typography',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                    properties: [
                        {name: 'Font', property: 'font-family'},
                        {name: 'Weight', property: 'font-weight'},
                        {name: 'Font color', property: 'color'},
                        {
                            property: 'text-align',
                            type: 'radio',
                            defaults: 'left',
                            list: [
                                {value: 'left', name: 'Left', className: 'fa fa-align-left'},
                                {value: 'center', name: 'Center', className: 'fa fa-align-center'},
                                {value: 'right', name: 'Right', className: 'fa fa-align-right'},
                                {value: 'justify', name: 'Justify', className: 'fa fa-align-justify'}
                            ],
                        }, {
                            property: 'text-decoration',
                            type: 'radio',
                            defaults: 'none',
                            list: [
                                {value: 'none', name: 'None', className: 'fa fa-times'},
                                {value: 'underline', name: 'underline', className: 'fa fa-underline'},
                                {value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough'}
                            ],
                        }, {
                            property: 'text-shadow',
                            properties: [
                                {name: 'X position', property: 'text-shadow-h'},
                                {name: 'Y position', property: 'text-shadow-v'},
                                {name: 'Blur', property: 'text-shadow-blur'},
                                {name: 'Color', property: 'text-shadow-color'}
                            ],
                        }],
                }, {
                    name: 'Decorations',
                    open: false,
                    buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                    properties: [{
                        type: 'slider',
                        property: 'opacity',
                        defaults: 1,
                        step: 0.01,
                        max: 1,
                        min: 0,
                    }, {
                        property: 'border-radius',
                        properties: [
                            {name: 'Top', property: 'border-top-left-radius'},
                            {name: 'Right', property: 'border-top-right-radius'},
                            {name: 'Bottom', property: 'border-bottom-left-radius'},
                            {name: 'Left', property: 'border-bottom-right-radius'}
                        ],
                    }, {
                        property: 'box-shadow',
                        properties: [
                            {name: 'X position', property: 'box-shadow-h'},
                            {name: 'Y position', property: 'box-shadow-v'},
                            {name: 'Blur', property: 'box-shadow-blur'},
                            {name: 'Spread', property: 'box-shadow-spread'},
                            {name: 'Color', property: 'box-shadow-color'},
                            {name: 'Shadow type', property: 'box-shadow-type'}
                        ],
                    }, {
                        property: 'background',
                        properties: [
                            {name: 'Image', property: 'background-image'},
                            {name: 'Repeat', property: 'background-repeat'},
                            {name: 'Position', property: 'background-position'},
                            {name: 'Attachment', property: 'background-attachment'},
                            {name: 'Size', property: 'background-size'}
                        ],
                    },],
                }, {
                    name: 'Extra',
                    open: false,
                    buildProps: ['transition', 'perspective', 'transform'],
                    properties: [{
                        property: 'transition',
                        properties: [
                            {name: 'Property', property: 'transition-property'},
                            {name: 'Duration', property: 'transition-duration'},
                            {name: 'Easing', property: 'transition-timing-function'}
                        ],
                    }, {
                        property: 'transform',
                        properties: [
                            {name: 'Rotate X', property: 'transform-rotate-x'},
                            {name: 'Rotate Y', property: 'transform-rotate-y'},
                            {name: 'Rotate Z', property: 'transform-rotate-z'},
                            {name: 'Scale X', property: 'transform-scale-x'},
                            {name: 'Scale Y', property: 'transform-scale-y'},
                            {name: 'Scale Z', property: 'transform-scale-z'}
                        ],
                    }]
                }, {
                    name: 'Flex',
                    open: false,
                    properties: [{
                        name: 'Flex Container',
                        property: 'display',
                        type: 'select',
                        defaults: 'block',
                        list: [
                            {value: 'block', name: 'Disable'},
                            {value: 'flex', name: 'Enable'}
                        ],
                    }, {
                        name: 'Flex Parent',
                        property: 'label-parent-flex',
                        type: 'integer',
                    }, {
                        name: 'Direction',
                        property: 'flex-direction',
                        type: 'radio',
                        defaults: 'row',
                        list: [{
                            value: 'row',
                            name: 'Row',
                            className: 'icons-flex icon-dir-row',
                            title: 'Row',
                        }, {
                            value: 'row-reverse',
                            name: 'Row reverse',
                            className: 'icons-flex icon-dir-row-rev',
                            title: 'Row reverse',
                        }, {
                            value: 'column',
                            name: 'Column',
                            title: 'Column',
                            className: 'icons-flex icon-dir-col',
                        }, {
                            value: 'column-reverse',
                            name: 'Column reverse',
                            title: 'Column reverse',
                            className: 'icons-flex icon-dir-col-rev',
                        }],
                    }, {
                        name: 'Justify',
                        property: 'justify-content',
                        type: 'radio',
                        defaults: 'flex-start',
                        list: [{
                            value: 'flex-start',
                            className: 'icons-flex icon-just-start',
                            title: 'Start',
                        }, {
                            value: 'flex-end',
                            title: 'End',
                            className: 'icons-flex icon-just-end',
                        }, {
                            value: 'space-between',
                            title: 'Space between',
                            className: 'icons-flex icon-just-sp-bet',
                        }, {
                            value: 'space-around',
                            title: 'Space around',
                            className: 'icons-flex icon-just-sp-ar',
                        }, {
                            value: 'center',
                            title: 'Center',
                            className: 'icons-flex icon-just-sp-cent',
                        }],
                    }, {
                        name: 'Align',
                        property: 'align-items',
                        type: 'radio',
                        defaults: 'center',
                        list: [{
                            value: 'flex-start',
                            title: 'Start',
                            className: 'icons-flex icon-al-start',
                        }, {
                            value: 'flex-end',
                            title: 'End',
                            className: 'icons-flex icon-al-end',
                        }, {
                            value: 'stretch',
                            title: 'Stretch',
                            className: 'icons-flex icon-al-str',
                        }, {
                            value: 'center',
                            title: 'Center',
                            className: 'icons-flex icon-al-center',
                        }],
                    }, {
                        name: 'Flex Children',
                        property: 'label-parent-flex',
                        type: 'integer',
                    }, {
                        name: 'Order',
                        property: 'order',
                        type: 'integer',
                        defaults: 0,
                        min: 0
                    }, {
                        name: 'Flex',
                        property: 'flex',
                        type: 'composite',
                        properties: [{
                            name: 'Grow',
                            property: 'flex-grow',
                            type: 'integer',
                            defaults: 0,
                            min: 0
                        }, {
                            name: 'Shrink',
                            property: 'flex-shrink',
                            type: 'integer',
                            defaults: 0,
                            min: 0
                        }, {
                            name: 'Basis',
                            property: 'flex-basis',
                            type: 'integer',
                            units: ['px', '%', ''],
                            unit: '',
                            defaults: 'auto',
                        }],
                    }, {
                        name: 'Align',
                        property: 'align-self',
                        type: 'radio',
                        defaults: 'auto',
                        list: [{
                            value: 'auto',
                            name: 'Auto',
                        }, {
                            value: 'flex-start',
                            title: 'Start',
                            className: 'icons-flex icon-al-start',
                        }, {
                            value: 'flex-end',
                            title: 'End',
                            className: 'icons-flex icon-al-end',
                        }, {
                            value: 'stretch',
                            title: 'Stretch',
                            className: 'icons-flex icon-al-str',
                        }, {
                            value: 'center',
                            title: 'Center',
                            className: 'icons-flex icon-al-center',
                        }],
                    }]
                }
                ],
            },
        },

    });

    // The upload is started
    editor.on('asset:upload:start', function () {
        toastr.success('Uploading image...');
    });

    // Error handling
    editor.on('asset:upload:error', function (err) {
        console.warn(err);
    });

    // Do something on response
    editor.on('asset:upload:response', function (response) {
        if (response.error) {
            console.warn(response.message);
        } else {
            toastr.success('Upload successfully. <br />Select image in right list to add to page!');
        }
    });

    var pn = editor.Panels;
    editor.Commands.add('canvas-clear', function () {
        if (confirm('Are you sure to clean the canvas?')) {
            editor.DomComponents.clear();
            setTimeout(function () {
                localStorage.clear()
            }, 0)
        }
    });

    // Simple warn notifier
    var origWarn = console.warn;
    toastr.options = {
        closeButton: true,
        preventDuplicates: true,
        showDuration: 250,
        hideDuration: 150
    };
    console.warn = function (msg) {
        if (msg.indexOf('[undefined]') === -1) {
            toastr.warning(msg);
        }
        origWarn(msg);
    };

    // Add and beautify tooltips
    [['sw-visibility', 'Show Borders'], ['preview', 'Preview'], ['fullscreen', 'Fullscreen'],
        ['export-template', 'Export'], ['undo', 'Undo'], ['redo', 'Redo'],
        ['gjs-open-import-webpage', 'Import'], ['canvas-clear', 'Clear All']]
        .forEach(function (item) {
            pn.getButton('options', item[0]).set('attributes', {title: item[1], 'data-tooltip-pos': 'bottom'});
        });
    [['open-sm', 'Style Manager'], ['open-layers', 'Layers'], ['open-blocks', 'Blocks']]
        .forEach(function (item) {
            pn.getButton('views', item[0]).set('attributes', {title: item[1], 'data-tooltip-pos': 'bottom'});
        });
    var titles = document.querySelectorAll('*[title]');

    for (var i = 0; i < titles.length; i++) {
        var el = titles[i];
        var title = el.getAttribute('title');
        title = title ? title.trim() : '';
        if (!title) {
            break;
        }
        el.setAttribute('data-tooltip', title);
        el.setAttribute('title', '');
    }

    // Show borders by default
    pn.getButton('options', 'sw-visibility').set('active', 1);

    // Do stuff on load
    editor.on('load', function () {
        var $ = grapesjs.$;

        // Load and show settings and style manager
        var openTmBtn = pn.getButton('views', 'open-tm');
        openTmBtn && openTmBtn.set('active', 1);
        var openSm = pn.getButton('views', 'open-sm');
        openSm && openSm.set('active', 1);

        // Add Settings Sector
        var traitsSector = $('<div class="gjs-sm-sector no-select">' +
            '<div class="gjs-sm-title"><span class="icon-settings fa fa-cog"></span> Settings</div>' +
            '<div class="gjs-sm-properties" style="display: none;"></div></div>');
        var traitsProps = traitsSector.find('.gjs-sm-properties');
        traitsProps.append($('.gjs-trt-traits'));
        $('.gjs-sm-sectors').before(traitsSector);
        traitsSector.find('.gjs-sm-title').on('click', function () {
            var traitStyle = traitsProps.get(0).style;
            var hidden = traitStyle.display === 'none';
            if (hidden) {
                traitStyle.display = 'block';
            } else {
                traitStyle.display = 'none';
            }
        });

        // Open block manager
        var openBlocksBtn = editor.Panels.getButton('views', 'open-blocks');
        openBlocksBtn && openBlocksBtn.set('active', 1);
    });

    pn.addButton('options', {
        id: 'back-to-page-button',
        className: 'button-option-cancel',
        label: '{{ __('Back to editing page') }}',
        command: 'back-to-edit-page',
        active: false
    });

    pn.addButton('options', {
        id: 'save-data-button',
        className: 'button-option-save',
        label: '{{ trans('plugins/page-builder::page-builder.save') }}',
        command: 'save-page',
        active: true
    });
</script>
</body>
</html>
