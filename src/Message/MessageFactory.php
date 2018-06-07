<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-5-22
 * Time: 下午6:55
 */

namespace Dezsidog\YzSdk\Message;


class MessageFactory
{
    public static function create($data)
    {
        switch ($data['type']) {
            case 'TRADE_ORDER_STATE':
                return new TradeOrderState($data);
            case 'COUPON_PROMOTION':
            case 'COUPON_CUSTOMER_PROMOTION':
                return new Coupon($data);
            default:
                throw new \InvalidArgumentException('unsupported message');
        }
    }
}