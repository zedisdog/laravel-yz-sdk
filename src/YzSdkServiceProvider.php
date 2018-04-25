<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-25
 * Time: 下午8:14
 */

namespace Dezsidog\YzSdk;


use Illuminate\Support\ServiceProvider;

class YzSdkServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(YzOpenSdk::class,function(){
            // 有赞商家id
            $seller_id = \Request::input('kdt_id');

            if ($seller_id) {
                // 先尝试取之前的yz token
                $access_token = \Cache::tags('yz_seller_'.$seller_id)->get('access_token');
                $refresh_token = \Cache::tags('yz_seller_'.$seller_id)->get('refresh_token');
                return new YzOpenSdk($access_token, $refresh_token);
            }else{
                return new YzOpenSdk();
            }
        });
    }

    public function boot()
    {

    }

    public function provides()
    {
        return [YzOpenSdk::class];
    }
}