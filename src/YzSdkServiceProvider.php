<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-25
 * Time: 下午8:14
 */

namespace Dezsidog\YzSdk;


use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class YzSdkServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(YzOpenSdk::class,function($app){
            return new YzOpenSdk($app);
        });
    }

    public function boot()
    {
        if ($this->app instanceof Application) {
            $this->publishes([
                __DIR__.'/config.php' => config_path('yz.php'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/config.php', 'yz');
        /**
         * @var Router $router
         */
        $router = $this->app->make('router');
        if (config('yz.callback.class')) {
            $router->prefix(config('yz.callback.prefix', 'api'))
                ->middleware(config('yz.callback.middlewares', 'api'))
                ->any(config('yz.callback.url', 'yz-callback'), config('callback.class'));
        }
        $router->prefix(config('yz.hook.prefix', 'api'))
            ->middleware(config('yz.hook.middlewares'), 'api')
            ->any(config('yz.hook.url'), config('yz.hook.action'));
    }

    public function provides()
    {
        return [YzOpenSdk::class];
    }
}