<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-5-22
 * Time: 下午6:55
 */

namespace Dezsidog\YzSdk\Message;


use Dezsidog\YzSdk\TradeOrderState;

class MessageFactory
{
    public static function create($data)
    {
        switch ($data['type']) {
            case 'TRADE_ORDER_STATE':
                return new TradeOrderState($data);
            default:
                throw new \InvalidArgumentException('unsupported message');
        }
    }
}