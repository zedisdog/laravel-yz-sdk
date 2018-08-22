<?php
/**
 * Created by PhpStorm.
 * User: dezsidog
 * Date: 18-5-22
 * Time: 下午11:19
 */

namespace Dezsidog\YzSdk\Test;


use Dezsidog\YzSdk\Message\Coupon;
use Dezsidog\YzSdk\Message\MessageFactory;
use Dezsidog\YzSdk\Message\TradeOrderState;
use PHPUnit\Framework\TestCase;

class MessageFactoryTest extends TestCase
{
    public function testNew()
    {
        $data = '{
            "msg": "%7B%22id%22%3A%222035015%22%2C%22type%22%3A%22COUPON_PROMOTION%22%2C%22status%22%3A%22CREATED_CARD%22%2C%22event_time%22%3A%222017-09-22+11%3A03%3A54%22%7D",
            "kdt_name": "测试店铺",
            "test": false,
            "sign": "4f8e1ff412c8b7ac83be459b09de9166",
            "sendCount": 0,
            "type": "COUPON_PROMOTION",
            "version": 1505721997,
            "client_id": "3931c2c6b39ccc0673",
            "mode": 1,
            "kdt_id": 12345,
            "id": "2035015",
            "status": "CREATED_CARD"
        }';
        $message = MessageFactory::create(json_decode($data, true));
        $this->assertInstanceOf(Coupon::class, $message);

        $data = '{
            "client_id":"6cd25b3f99727975b5",
            "id":"E20170807181905034500002",
            "kdt_id":63077,
            "kdt_name":"Qi码运动馆",
            "mode":1,
            "msg":"%7B%22update_time%22:%222017-08-07%2018:19:05%22,%22close_reason%22:%22NONE%22,%22tid%22:%22E20170807181905034500002%22,%22close_type%22:1%7D",
            "sendCount":0,
            "sign":"5c15274ca4c079197c89154f44b20307",
            "status":"TRADE_CLOSED",
            "test":false,
            "type":"trade_TradeClose",
            "version":1502101273
        }';
        $message = MessageFactory::create(json_decode($data, true));
        $this->assertInstanceOf(TradeOrderState::class, $message);
    }
}