<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-25
 * Time: 下午8:30
 */
return [
    // 有赞授权回调路由名称
    'callback' => '',
    // 有赞推送钩子
    'hook' => [
        'prefix' => 'api',
        'middlewares' => 'api',
        'url' => 'yz-hook',
        'action' => '\Dezsidog\YzSdk\Http\HookController@handler'
    ],
    'client_id' => env('YZ_CLIENT_ID', ""),
    'client_secret' => env('YZ_CLIENT_SECRET', ""),
    'multi_seller' => true,
];