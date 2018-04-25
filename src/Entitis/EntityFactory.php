<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午3:27
 */
declare(strict_types=1);

namespace Dezsidog\YzSdk\Entitis;


use Dezsidog\YzSdk\Contracts\Entity;
use Dezsidog\YzSdk\Message;

class EntityFactory
{
    public static function create(string $type, string $content): Entity
    {
        switch ($type) {
            case Message::TYPE_TRADE_ORDER_STATE:
                return new Trade($content);
                break;
            case Message::TYPE_TRADE_ORDER_REFUND:
                return new Refund($content);
                break;
            case Message::TYPE_ITEM_STATE:
                $data = json_decode(urldecode($content),true)['data'];
                return new ProductStatus($data);
                break;
            case Message::TYPE_ITEM_INFO:
                $data = json_decode(urldecode($content),true)['data'];
                return new ProductBasic($data);
                break;
            case Message::TYPE_ITEM_SKU_INFO:
                $data = json_decode(urldecode($content),true)['data'];
                return new ProductSku($data);

        }

        return null;
    }
}
