<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-28
 * Time: 上午9:50
 */

namespace Dezsidog\YzSdk\Http;


use Dezsidog\YzSdk\Events\ReceivedYzMessage;
use Dezsidog\YzSdk\Events\redis\ReceivedYzMessage as QueueReceivedYzMessage;
use Dezsidog\YzSdk\Message\MessageFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HookController extends Controller
{
    public function handler(Request $request)
    {
        $log = app()->make('log');
        $log->info('yz-message-receive', $request->input() ?? []);
        if ($request->has('type')) {
            $message = MessageFactory::create($request->input());
            if (config('yz.hook.event_should_queue')) {
                event(new QueueReceivedYzMessage($message));
            } else {
                event(new ReceivedYzMessage($message));
            }
        }

        die('{"code":0,"msg":"success"}');
    }
}