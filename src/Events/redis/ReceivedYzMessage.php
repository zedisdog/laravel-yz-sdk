<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-28
 * Time: 上午9:48
 */

namespace Dezsidog\YzSdk\Events\redis;


use Dezsidog\YzSdk\Message\BaseMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReceivedYzMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var BaseMessage
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param BaseMessage $message
     */
    public function __construct(BaseMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}