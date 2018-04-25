<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午2:32
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Entitis;


use Carbon\Carbon;

/**
 * 商品基础信息事件 ITEM_INFO
 * Class ProductBasic
 * @package App\Lib\Entitis
 * @property-read integer $item_id
 * @property-read string $alias
 * @property-read string $title
 * @property-read integer $price
 * @property-read integer $type_id
 * @property-read integer $total_stock
 * @property-read integer $num
 */
class ProductBasic extends BaseEntity
{
    protected $json2arrays = [
        'tag',
        'picture'
    ];
    /**
     * 变更的字段
     * @var
     */
    protected $change_fields;
    /**
     * 商品ID
     * @var integer
     */
    protected $item_id;
    /**
     * 商品别名
     * @var string
     */
    protected $alias;
    /**
     * 店铺ID
     * @var integer
     */
    protected $kdt_id;
    /**
     * 标题
     * @var string
     */
    protected $title;
    /**
     * 商品简介
     * @var string
     */
    protected $sub_title;
    /**
     * 价格(分)
     * @var integer
     */
    protected $price;
    /**
     * 显示在原价那里的信息
     * @var string
     */
    protected $origin;
    /**
     * 统一运费
     * @var integer
     */
    protected $postage;
    /**
     * 序号
     * @var integer
     */
    protected $num;
    /**
     * 商品类型 0默认类型 1拍卖 10分销
     * @var integer
     */
    protected $goods_type;
    /**
     * 货号
     * @var string
     */
    protected $goods_no;
    /**
     * @var integer 虚拟商品1是 0否
     */
    protected $is_virtual;
    /**
     * 用户购买限额
     * @var integer
     */
    protected $quota;
    /**
     * @var string 留言信息
     */
    protected $messages;
    /**
     * @var integer 是否参加会员折扣,默认：0 不参加
     */
    protected $join_level_discount;
    /**
     * @var integer 运费模板ID
     */
    protected $delivery_template_id;
    /**
     * @var array 商品分组
     */
    protected $tag;
    /**
     * @var integer 隐藏库存
     */
    protected $hide_stock;
    /**
     * @var integer 总库存
     */
    protected $total_stock;
    /**
     * @var array 商品主图
     */
    protected $picture;
    /**
     * @var int	图片高度
     */
    protected $picture_height;
    /**
     * @var Carbon 定时开售时间
     */
    protected $start_sold_time;
    /**
     * @var string 商品详情
     */
    protected $content;
    /**
     * @var string 组件
     */
    protected $components;
}
