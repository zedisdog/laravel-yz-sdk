<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午2:20
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Entitis;

/**
 * 商品状态事件 ITEM_STATE
 * Class ProductStatus
 * @package App\Lib\Entitis
 * @property-read integer $item_id
 */
class ProductStatus extends BaseEntity
{
    /**
     * 出售中
     */
    const SELLING = 1;
    /**
     * 售罄
     */
    const OUT = 2;
    /**
     * 部分售罄
     */
    const PART_OUT = 3;
    /**
     * 上架
     */
    const DISPLAY = 0;
    /**
     * 下架
     */
    const HIDDEN = 1;
    /**
     * 商品id
     * @var integer
     */
    protected $item_id;
    /**
     * 商品别名
     * @var string
     */
    protected $alias;
    /**
     * 店铺id
     * @var integer
     */
    protected $kdt_id;
    /**
     * 标题
     * @var string
     * 只删除事件有此字段
     */
    protected $title;
    /**
     * 销售状态
     * @var integer
     * 只售罄事件返回此字段
     */
    protected $sold_status;
    /**
     * 上下架状态
     * @var integer
     * 只上下架事件返回此字段
     */
    protected $is_display;
}
