<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 上午10:43
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk;

use Dezsidog\YzSdk\Message\BaseMessage;

/**
 * 有赞推送消息
 * Class Message
 * @package App\Lib
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

    public function setMsgAttribute($value)
    {
        $msg = json_decode(urldecode($value), true);
        $this->setAttribute('payment', $msg['payment']);
        $this->setAttribute('update_time', $msg['update_time']);
    }
}
