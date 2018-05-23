<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-5-22
 * Time: 下午5:42
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Message;


use Illuminate\Database\Eloquent\Concerns\HasAttributes;

/**
 * Class BaseMessage
 * @package Dezsidog\YzSdk\Message
 * @property-read string $type 消息类型
 */
abstract class BaseMessage
{
    use HasAttributes { setAttribute as baseSetAttribute; }

    /**
     * 订单状态事件
     */
    const TRADE_ORDER_STATE = 'TRADE_ORDER_STATE';
    /**
     * 订单备注事件
     */
    const TRADE_ORDER_REMARK = 'TRADE_ORDER_REMARK';
    /**
     * 退款事件
     */
    const TRADE_ORDER_REFUND = 'TRADE_ORDER_REFUND';
    /**
     * 物流事件
     */
    const TRADE_ORDER_EXPRESS = 'TRADE_ORDER_EXPRESS';
    /**
     * 商品状态事件
     */
    const ITEM_STATE = 'ITEM_STATE';
    /**
     * 商品基础信息事件
     */
    const ITEM_INFO = 'ITEM_INFO';
    /**
     * 商家端会员卡事件
     */
    const SCRM_CARD = 'SCRM_CARD';
    /**
     * 用户端会员卡事件
     */
    const SCRM_CUSTOMER_CARD = 'SCRM_CUSTOMER_CARD';
    /**
     * 积分消息
     */
    const POINTS = 'POINTS';
    /**
     * 商品规格信息事件
     */
    const ITEM_SKU_INFO = 'ITEM_SKU_INFO';
    /**
     * 商家端优惠券码/事件
     */
    const COUPON_PROMOTION = 'COUPON_PROMOTION';
    /**
     * 用户端优惠券/码事件
     */
    const COUPON_CUSTOMER_PROMOTION = 'COUPON_CUSTOMER_PROMOTION';
    /**
     * 客户消息事件
     */
    const SCRM_CUSTOMER_EVENT = 'SCRM_CUSTOMER_EVENT';
    /**
     * 自定义会员价消息事件
     */
    const CUSTOMER_DISCOUNT = 'CUSTOMER_DISCOUNT';


    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    public function getDates()
    {
        return $this->dates;
    }

    public function getCasts()
    {
        return $this->casts;
    }

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    public function setAttribute($key, $value)
    {
        $key = snake_case($key);
        return $this->baseSetAttribute($key, $value);
    }

    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    public function fill($data)
    {
        // msg会根据不同的type来实例化，所以要先等type被赋值，干脆就把msg放到最后一个赋值
        $msg_key = '';
        $msg = '';
        foreach ($data as $key => $datum) {
            if (preg_match('/^msg$/i', $key)) {
                $msg_key = $key;
                break;
            }
        }

        if ($msg_key) {
            $msg = $data[$msg_key];
            unset($data[$msg_key]);
        }

        foreach ($data as $key => $value) {
            $this->setAttribute($key, $value);
        }

        if ($msg) {
            $this->setAttribute($msg_key, $msg);
        }
    }
}