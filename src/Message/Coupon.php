<?php
/**
 * Created by PhpStorm.
 * User: dezsidog
 * Date: 18-5-22
 * Time: 下午10:02
 */

namespace Dezsidog\YzSdk\Message;
use Carbon\Carbon;

/**
 * Class Coupon
 * @package Dezsidog\YzSdk\Message
 * @property-read string $kdt_name 店铺名称
 * @property-read bool $test 是否是测试消息
 * @property-read string $sign 签名
 * @property-read integer $send_count
 * @property-read string $type 类型
 * @property-read integer $version 版本
 * @property-read string $client_id
 * @property-read integer $mode
 * @property-read integer $kdt_id 店铺id
 * @property-read integer $id 优惠券id
 * @property-read string $status 状态
 * @property-read Carbon $event_time 发生时间
 */
class Coupon extends BaseMessage
{
    /**
     * 商家创建优惠券活动
     */
    const CREATED_CARD = 'CREATED_CARD';
    /**
     * 商家更新优惠券活动
     */
    const UPDATED_CARD = 'UPDATED_CARD';
    /**
     * 商家失效优惠券活动
     */
    const CARD_GROUP_INVALID = 'CARD_GROUP_INVALID';
    /**
     * 商家创建优惠码活动
     */
    const CODE_CREATED = 'CODE_CREATED';
    /**
     * 商家更新优惠码活动
     */
    const CODE_UPDATED = 'CODE_UPDATED';
    /**
     * 商家失效优惠码活动
     */
    const CODE_GROUP_INVALID = 'CODE_GROUP_INVALID';

    protected $dates = [
        'event_time'
    ];

    public function setMsgAttribute($value)
    {
        $msg = json_decode(urldecode($value), true);
        $this->setAttribute('event_time', $msg['event_time']);
    }
}