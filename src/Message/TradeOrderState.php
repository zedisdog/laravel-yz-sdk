<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 上午10:43
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Message;

use Carbon\Carbon;

/**
 * 有赞推送消息
 * Class Message
 * @package App\Lib
 * @property-read string $client_id
 * @property-read string $id 订单id
 * @property-read string $tid 订单id
 * @property-read integer $kdt_id 店铺id
 * @property-read string $kdt_name 店铺名称
 * @property-read integer $mode
 * @property-read integer $send_count
 * @property-read string $sign 签名
 * @property-read string $status 订单状态
 * @property-read bool $test 是否是测试消息
 * @property-read string $type 类型
 * @property-read integer $version 版本
 * @property-read integer $payment 金额
 * @property-read Carbon $update_time 更新时间
 *
 */
class TradeOrderState extends BaseMessage
{
    /**
     * 等待买家付款
     */
    const WAIT_BUYER_PAY = 'WAIT_BUYER_PAY';
    /**
     * 待确认，包括（待成团：拼团订单、待接单：外卖订单）
     */
    const WAIT_CONFIRM = 'WAIT_CONFIRM';
    /**
     * 等待卖家发货，即:买家已付款
     */
    const WAIT_SELLER_SEND_GOODS = 'WAIT_SELLER_SEND_GOODS';
    /**
     * 等待买家确认收货,即:卖家已发货
     */
    const WAIT_BUYER_CONFIRM_GOODS = 'WAIT_BUYER_CONFIRM_GOODS';
    /**
     * 买家已签收
     */
    const TRADE_BUYER_SIGNED = 'TRADE_BUYER_SIGNED';
    /**
     * 交易成功
     */
    const TRADE_SUCCESS = 'TRADE_SUCCESS';
    /**
     * 交易关闭
     */
    const TRADE_CLOSED = 'TRADE_CLOSED';

    protected $dates = [
        'update_time'
    ];

    public function setPaymentAttribute($value)
    {
        $this->attributes['payment'] = intval($value * 100);
    }

    public function setMsgAttribute($value)
    {
        $msg = json_decode(urldecode($value), true);
        $this->setAttribute('payment', $msg['payment']);
        $this->setAttribute('update_time', $msg['update_time']);
    }

    public function getTidAttribute()
    {
        return $this->getAttribute('id');
    }
}
