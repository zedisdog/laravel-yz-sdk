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
            "msg":"%7B%22update_time%22:%222017-08-07%2018:19:05%22,%22close_reason%22:%22NONE%22,%22tid%22:%22E20170807181905034500002%22,%22close_type%22:1%7D",
            "sendCount":0,
            "sign":"5c15274ca4c079197c89154f44b20307",
            "status":"TRADE_CLOSED",
            "test":false,
            "type":"trade_TradeClose",
            "version":1502101273
        }';
        $message = new TradeOrderState(json_decode($data, true));
        $this->assertInstanceOf(TradeOrderState::class, $message);
        $this->assertEquals(null, $message->update_time);
        $this->assertEquals(null, $message->payment);
        $this->assertEquals($message->tid, $message->id);
        $this->assertEquals(TradeOrderState::TRADE_CLOSED, $message->status);
    }

    public function testWithTrade()
    {
        $data = '{
            "client_id":"c63054ee1979253abd",
            "id":"E20180822120606008800010",
            "kdt_id":40151071,
            "kdt_name":"小农年货优选",
            "mode":1,
            "msg":"%7B%22order_promotion%22:%7B%22adjust_fee%22:%220.00%22%7D,%22refund_order%22:[],%22full_order_info%22:%7B%22address_info%22:%7B%22self_fetch_info%22:%22%22,%22delivery_address%22:%22%E8%A1%97%E9%81%93%22,%22delivery_postal_code%22:%22610000%22,%22receiver_name%22:%22%E5%BC%A0%E5%93%B2%22,%22delivery_province%22:%22%E5%9B%9B%E5%B7%9D%E7%9C%81%22,%22delivery_city%22:%22%E6%88%90%E9%83%BD%E5%B8%82%22,%22delivery_district%22:%22%E6%AD%A6%E4%BE%AF%E5%8C%BA%22,%22address_extra%22:%22%7B%5C%22areaCode%5C%22:%5C%22510107%5C%22,%5C%22lon%5C%22:104.06506712178083,%5C%22lat%5C%22:30.6418120378037%7D%22,%22receiver_tel%22:%2215281009123%22%7D,%22remark_info%22:%7B%22buyer_message%22:%22%22%7D,%22pay_info%22:%7B%22outer_transactions%22:[],%22post_fee%22:%220.00%22,%22total_fee%22:%222.50%22,%22payment%22:%222.50%22,%22transaction%22:[]%7D,%22buyer_info%22:%7B%22buyer_phone%22:%2215281009123%22,%22fans_type%22:1,%22buyer_id%22:695120984,%22fans_id%22:4851134360,%22fans_nickname%22:%22z%3F%3F%3F%3F%22%7D,%22orders%22:[%7B%22outer_sku_id%22:%22%22,%22goods_url%22:%22https://h5.youzan.com/v2/showcase/goods%3Falias=2xad0v6acqbkv%22,%22item_id%22:410480150,%22outer_item_id%22:%22%22,%22item_type%22:0,%22num%22:1,%22sku_id%22:0,%22sku_properties_name%22:%22[]%22,%22pic_path%22:%22https://img.yzcdn.cn/upload_files/2018/02/02/FjEoOLM41hAscmf_WSSYNLDb6VNi.png%22,%22oid%22:%221467324204620719891%22,%22title%22:%22%E5%AE%9E%E7%89%A9%E5%95%86%E5%93%813%22,%22buyer_messages%22:%22%22,%22is_present%22:false,%22points_price%22:%220%22,%22price%22:%222.50%22,%22total_fee%22:%222.50%22,%22alias%22:%222xad0v6acqbkv%22,%22payment%22:%222.50%22%7D],%22source_info%22:%7B%22is_offline_order%22:false,%22source%22:%7B%22platform%22:%22wx%22,%22wx_entrance%22:%22direct_buy%22%7D%7D,%22order_info%22:%7B%22consign_time%22:%22%22,%22order_extra%22:%7B%22is_from_cart%22:%22false%22,%22is_points_order%22:%220%22%7D,%22created%22:%222018-08-22%2012:06:06%22,%22status_str%22:%22%E5%BE%85%E6%94%AF%E4%BB%98%22,%22expired_time%22:%222018-08-22%2013:06:06%22,%22success_time%22:%22%22,%22type%22:0,%22tid%22:%22E20180822120606008800010%22,%22confirm_time%22:%22%22,%22pay_time%22:%22%22,%22update_time%22:%222018-08-22%2012:06:06%22,%22pay_type_str%22:%22%22,%22is_retail_order%22:false,%22pay_type%22:0,%22team_type%22:0,%22refund_state%22:0,%22close_type%22:0,%22status%22:%22WAIT_BUYER_PAY%22,%22express_type%22:0,%22order_tags%22:%7B%22is_message_notify%22:true,%22is_use_ump%22:true,%22is_secured_transactions%22:true%7D%7D%7D%7D",
            "msg_id":"fdd814f9-8c04-4e72-afef-1b595af58676",
            "sendCount":0,
            "sign":"a54d68a2044bca3622bc30ba93ca688e",
            "status":"WAIT_BUYER_PAY",
            "test":false,
            "type":"trade_TradeCreate",
            "version":1534910766
        }';
        $message = new TradeOrderState(json_decode($data, true));
        $this->assertInstanceOf(TradeOrderState::class, $message);
        $this->assertInstanceOf(Carbon::class, $message->update_time);
        $this->assertInternalType('integer', $message->payment);
        $this->assertEquals(250, $message->payment);
        $this->assertEquals($message->tid, $message->id);
        $this->assertEquals(TradeOrderState::WAIT_BUYER_PAY, $message->status);
        $this->assertEquals('trade_TradeCreate', $message->type);
    }
}