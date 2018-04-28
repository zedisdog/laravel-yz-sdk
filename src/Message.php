<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 上午10:43
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk;


use Dezsidog\YzSdk\Contracts\Entity;
use Dezsidog\YzSdk\Entitis\EntityFactory;
use Illuminate\Http\Request;

/**
 * 有赞推送消息
 * Class Message
 * @package App\Lib
 * @property-read string $type
 * @property-read Entity $msg
 * @property-read integer $kdt_id
 * @property-read string $status
 */
class Message
{
    /**
     * 普通模式
     * 自用型/工具型/平台型消息
     */
    const MODE_NORMAL = 1;
    /**
     * 签名模式
     */
    const MODE_SIGN = 0;
    /**
     * 订单状态事件
     */
    const TYPE_TRADE_ORDER_STATE = 'TRADE_ORDER_STATE';
    /**
     * 退款事件
     */
    const TYPE_TRADE_ORDER_REFUND = 'TRADE_ORDER_REFUND';
    /**
     * 物流事件
     */
    const TYPE_TRADE_ORDER_EXPRESS = 'TRADE_ORDER_EXPRESS';
    /**
     * 商品状态事件
     */
    const TYPE_ITEM_STATE = 'ITEM_STATE';
    /**
     * 商品基础信息事件
     */
    const TYPE_ITEM_INFO = 'ITEM_INFO';
    /**
     * 积分事件
     */
    const TYPE_POINTS = 'POINTS';
    /**
     * 会员卡(商家端)
     */
    const TYPE_SCRM_CARD = 'SCRM_CARD';
    /**
     * 会员卡(用户端)
     */
    const TYPE_SCRM_CUSTOMER_CARD = 'SCRM_CUSTOMER_CARD';
    /**
     * 交易v1
     */
    const TYPE_TRADE = 'TRADE';
    /**
     * 商品v1
     */
    const TYPE_ITEM = 'ITEM';
    /**
     * 商品sku事件
     */
    const TYPE_ITEM_SKU_INFO = 'ITEM_SKU_INFO';
    /**
     * 等待买家付款
     */
    const STATUS_WAIT_BUYER_PAY = 'WAIT_BUYER_PAY';
    /**
     * 待确认 包括（待成团：拼团订单、待接单：外卖订单）
     */
    const STATUS_WAIT_CONFIRM = 'WAIT_CONFIRM';
    /**
     * 等待卖家发货，即:买家已付款
     */
    const STATUS_WAIT_SELLER_SEND_GOODS = 'WAIT_SELLER_SEND_GOODS';
    /**
     * 等待买家确认收货,即:卖家已发货
     */
    const STATUS_WAIT_BUYER_CONFIRM_GOODS = 'WAIT_BUYER_CONFIRM_GOODS';
    /**
     * 买家已签收
     */
    const STATUS_TRADE_BUYER_SIGNED = 'TRADE_BUYER_SIGNED';
    /**
     * 交易成功
     */
    const STATUS_TRADE_SUCCESS = 'TRADE_SUCCESS';
    /**
     * 交易关闭
     */
    const STATUS_TRADE_CLOSED = 'TRADE_CLOSED';
    /**
     * 商品删除
     */
    const STATUS_ITEM_DELETE = 'ITEM_DELETE';
    /**
     * 部分售罄（多sku商品某sku售罄）
     */
    const STATUS_SOLD_OUT_PART = 'SOLD_OUT_PART';
    /**
     * 全部售罄
     */
    const STATUS_SOLD_OUT_ALL = 'SOLD_OUT_ALL';
    /**
     * 售罄恢复
     */
    const STATUS_SOLD_OUT_REVERT = 'SOLD_OUT_REVERT';
    /**
     * 商品上架
     */
    const STATUS_ITEM_SALE_UP = 'ITEM_SALE_UP';
    /**
     * 商品下架
     */
    const STATUS_ITEM_SALE_DOWN = 'ITEM_SALE_DOWN';
    /**
     * 商家创建会员卡/优惠券/创建客户
     */
    const STATUS_CARD_CREATED = 'CARD_CREATED';
    /**
     * 商家更新会员卡
     */
    const STATUS_CARD_UPDATED = 'CARD_UPDATED';
    /**
     * 商家删除会员卡
     */
    const STATUS_CARD_DELETED = 'CARD_DELETED';
    /**
     * 商家禁用会员卡
     */
    const STATUS_CARD_DISABLED = 'CARD_DISABLED';
    /**
     * 商家启用会员卡
     */
    const STATUS_CARD_ENABLED = 'CARD_ENABLED';
    /**
     * 商家更新优惠券/更新客户
     */
    const STATUS_UPDATED_CARD = 'UPDATED_CARD';
    /**
     * 优惠券失效
     */
    const STATUS_CARD_GROUP_INVALID = 'CARD_GROUP_INVALID';
    /**
     * 创建优惠吗
     */
    const STATUS_CODE_CREATED = 'CODE_CREATED';
    /**
     * 更新优惠吗
     */
    const STATUS_CODE_UPDATED = 'CODE_UPDATED';
    /**
     * 优惠码失效
     */
    const STATUS_CODE_GROUP_INVALID = 'CODE_GROUP_INVALID';
    /**
     * 商家更新自定义会员价
     */
    const STATUS_CUSTOMER_DISCOUNT_UPDATED = 'CUSTOMER_DISCOUNT_UPDATED';
    /**
     * 创建商品
     */
    const STATUS_ITEM_CREATE = 'ITEM_CREATE';
    /**
     * 商品编辑
     */
    const STATUS_ITEM_UPDATE = 'ITEM_UPDATE';
    /**
     * 创建sku
     */
    const STATUS_SKU_CREATE = 'SKU_CREATE';
    /**
     * 删除sku
     */
    const STATUS_SKU_DELETE = 'SKU_DELETE';
    /**
     * sku编辑
     */
    const STATUS_SKU_UPDATE = 'SKU_UPDATE';

    /**
     * 消息模式
     * @var integer
     */
    protected $mode;
    /**
     * 订单消息为订单编号,会员卡消息为会员卡id
     * @var string
     */
    protected $id;
    /**
     * 开发者client_id
     * @var string
     */
    protected $client_id;
    /**
     * 消息业务类型
     * @var string
     */
    protected $type;
    /**
     * 消息状态
     * @var string
     */
    protected $status;
    /**
     * 店铺id
     * @var integer
     */
    protected $kdt_id;
    /**
     * 防伪签名
     * @var string
     */
    protected $sign;
    /**
     * 消息版本号
     * @var integer
     */
    protected $version;
    /**
     * 是否测试
     * @var bool
     */
    protected $test;
    /**
     * 消息
     * @var Entity
     */
    protected $msg;
    /**
     * 重发次数
     * @var integer
     */
    protected $send_count;

    public function __construct(Request $request)
    {
        foreach ($request->input() as $key => $item) {
            if (property_exists($this, $key)) {
                if ($key == 'msg'){
                    $this->$key = EntityFactory::create($request->input('type'), $item);
                }else{
                    $this->$key = $item;
                }
            }
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }
}
