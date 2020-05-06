<?php

namespace Botble\PageBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Route;

class PageBuilderServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/page-builder')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web'])
            ->publishAssets();

        add_filter(BASE_FILTER_FORM_EDITOR_BUTTONS, [$this, 'addPageBuilderButton'], 120, 1);
    }

    /**
     * @param string $data
     * @return string
     */
    public function addPageBuilderButton($data)
    {
        if (Route::currentRouteName() === 'pages.edit' && Route::current()->parameter('page')) {
            return $data . view('plugins/page-builder::button', ['id' => Route::current()->parameter('page')]);
        }

        return $data;
    }
}
