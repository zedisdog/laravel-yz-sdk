<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午3:10
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Entitis;

/**
 * 商品规格信息事件 ITEM_SKU_INFO
 * Class ProductSku
 * @package App\Lib\Entitis
 */
class ProductSku extends BaseEntity
{
    /**
     * @var integer 商品ID
     */
    protected $item_id;
    /**
     * @var integer 店铺ID
     */
    protected $kdt_id;
    /**
     * @var integer 规格库存数量
     */
    protected $stock_num;
    /**
     * @var integer 规格库存价格
     */
    protected $price;
    /**
     * @var string 规格商家编码
     */
    protected $code;
}
