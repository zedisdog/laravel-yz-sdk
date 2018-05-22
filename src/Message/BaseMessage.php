<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-5-22
 * Time: 下午5:42
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Message;


use Illuminate\Database\Eloquent\Concerns\HasAttributes;

abstract class BaseMessage
{
    use HasAttributes { setAttribute as baseSetAttribute; }

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    public function getDates()
    {
        return $this->dates;
    }

    public function getCasts()
    {
        return $this->casts;
    }

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    public function setAttribute($key, $value)
    {
        $key = snake_case($key);
        return $this->baseSetAttribute($key, $value);
    }

    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    public function fill($data)
    {
        // msg会根据不同的type来实例化，所以要先等type被赋值，干脆就把msg放到最后一个赋值
        $msg_key = '';
        $msg = '';
        foreach ($data as $key => $datum) {
            if (preg_match('/^msg$/i', $key)) {
                $msg_key = $key;
                break;
            }
        }

        if ($msg_key) {
            $msg = $data[$msg_key];
            unset($data[$msg_key]);
        }

        foreach ($data as $key => $value) {
            $this->setAttribute($key, $value);
        }

        if ($msg) {
            $this->setAttribute($msg_key, $msg);
        }
    }
}