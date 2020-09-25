<?php

namespace Encore\Grid\Lightbox;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Column;
use Illuminate\Support\ServiceProvider;

class LightboxServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Lightbox $extension)
    {
        if (! Lightbox::boot()) {
            return ;
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/ygmt/grid-lightbox')],
                'ygmt-grid-lightbox'
            );
        }

        Admin::booting(function () {

            Admin::css('vendor/ygmt/grid-lightbox/magnific-popup.css');
            Admin::js('vendor/ygmt/grid-lightbox/jquery.magnific-popup.min.js');
            Column::extend('lightbox', LightboxDisplayer::class);
            Column::extend('gallery', GalleryDisplayer::class);

	        Admin::css('vendor/ygmt/grid-lightbox/jquery.magnify.css');
	        Admin::js('vendor/ygmt/grid-lightbox/layer.js');
	        Column::extend('photo', PhotoDisplayer::class);
        });
    }
}