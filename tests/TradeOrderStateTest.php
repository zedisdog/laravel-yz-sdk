<?php
/**
 * Created by PhpStorm.
 * User: dezsidog
 * Date: 18-5-22
 * Time: 下午10:25
 */

namespace Dezsidog\YzSdk\Test;


use Carbon\Carbon;
use Dezsidog\YzSdk\Message\TradeOrderState;
use PHPUnit\Framework\TestCase;

class TradeOrderStateTest extends TestCase
{
    public function testNew()
    {
        $data = '{
            "client_id":"6cd25b3f99727975b5",
            "id":"E20170807181905034500002",
            "kdt_id":63077,
            "kdt_name":"Qi码运动馆",
            "mode":1,
            "msg":"%7B%22update_time%22:%222017-08-07%2018:19:05%22,%22payment%22:%2211.00%22,%22tid%22:%22E20170807181905034500002%22,%22status%22:%22TRADE_CLOSED%22%7D",
            "sendCount":0,
            "sign":"5c15274ca4c079197c89154f44b20307",
            "status":"TRADE_CLOSED",
            "test":false,
            "type":"TRADE_ORDER_STATE",
            "version":1502101273
        }';
        $message = new TradeOrderState(json_decode($data, true));
        $this->assertInstanceOf(TradeOrderState::class, $message);
        $this->assertInstanceOf(Carbon::class, $message->update_time);
        $this->assertInternalType('integer', $message->payment);
        $this->assertEquals(1100, $message->payment);
        $this->assertEquals($message->tid, $message->id);
        $this->assertEquals(TradeOrderState::TRADE_CLOSED, $message->status);
    }
}