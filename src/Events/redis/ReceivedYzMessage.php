<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-28
 * Time: 上午9:48
 */

namespace Dezsidog\YzSdk\Events\redis;


use Illuminate\Contracts\Queue\ShouldQueue;

class ReceivedYzMessage extends \Dezsidog\YzSdk\Events\ReceivedYzMessage implements ShouldQueue
{
}