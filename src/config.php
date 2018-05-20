<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-25
 * Time: 下午8:30
 */
return [
    // 事件是否需要使用队列
    'event_should_queue' => true,
    // 有赞授权回调路由名称
    'callback' => '',
    // 有赞推送钩子
    'hook' => [
        'prefix' => 'api',
        'middlewares' => 'api',
        'url' => 'yz-hook',
        'action' => '\Dezsidog\YzSdk\Http\HookController@handler'
    ],
    'client_id' => "",
    'client_secret' => "",
    'multi_seller' => true,
];