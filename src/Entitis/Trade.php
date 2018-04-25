<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午2:09
 */
declare(strict_types=1);

namespace Dezsidog\YzSdk\Entitis;


use Carbon\Carbon;

/**
 * 订单状态事件 TRADE_ORDER_STATE
 * Class Trade
 * @package App\Lib\Entitis
 */
class Trade extends BaseEntity
{
    protected $yuan2fens = [
        'payment'
    ];
    protected $dates = [
        'update_time'
    ];
    protected $map = [
        'tid' => 'id',
    ];
    /**
     * 订单号
     * @var string
     */
    public $id;
    /**
     * 状态
     * @var string
     */
    public $status;
    /**
     * 订单金额
     * @var Integer
     */
    public $payment;
    /**
     * 更新时间
     * @var Carbon
     */
    public $update_time;
}
