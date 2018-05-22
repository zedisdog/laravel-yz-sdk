<?php
/**
 * Created by PhpStorm.
 * User: dezsidog
 * Date: 18-5-22
 * Time: 下午11:07
 */

namespace Dezsidog\YzSdk\Test;


use Carbon\Carbon;
use Dezsidog\YzSdk\Message\Coupon;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
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
        $message = new Coupon(json_decode($data, true));
        $this->assertInstanceOf(Coupon::class, $message);
        $this->assertInstanceOf(Carbon::class, $message->event_time);
        $this->assertEquals(Coupon::CREATED_CARD, $message->status);
    }
}