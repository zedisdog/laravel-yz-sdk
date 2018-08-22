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
            case 'trade_TradeCreate':
            case 'trade_TradeClose':
            case 'trade_TradeSuccess':
            case 'trade_TradePartlySellerShip':
            case 'trade_TradeSellerShip':
            case 'trade_TradeBuyerPay':
                return new TradeOrderState($data);
            case 'COUPON_PROMOTION':
            case 'COUPON_CUSTOMER_PROMOTION':
                return new Coupon($data);
            default:
                if (class_exists(\Log::class)) {
                    \Log::warning("unsupported message <{$data['type']}>");
                }
                return null;
//                throw new \InvalidArgumentException('unsupported message');
        }
    }
}