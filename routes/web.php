<?php

Route::group(['namespace' => 'Botble\PageBuilder\Http\Controllers', 'middleware' => 'web'], function () {

    Route::group(['prefix' => config('core.base.general.admin_dir'), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'page-builders'], function () {

            Route::get('design/{id}', [
                'as'   => 'page_builder.design',
                'uses' => 'PageBuilderController@getDesign',
            ]);

            Route::put('design/{id}', [
                'as'         => 'page_builder.save-design',
                'uses'       => 'PageBuilderController@postSaveDesign',
                'permission' => 'page_builder.design',
            ]);

        });
    });

});