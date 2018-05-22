<?php
declare(strict_types=1);
namespace Dezsidog\YzSdk\Events;

use Dezsidog\YzSdk\Message\BaseMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReceivedYzMessage
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
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
