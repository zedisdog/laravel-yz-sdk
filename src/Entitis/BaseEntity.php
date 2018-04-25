<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午2:17
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk\Entitis;


use Dezsidog\YzSdk\Contracts\Entity;
use Carbon\Carbon;

abstract class BaseEntity implements Entity
{
    /**
     * 这个数组里面的字段将会被转换成Carbon
     * @var array
     */
    protected $dates = [];
    /**
     * 这个数组里面字段将会被认为单位是元,并且会被转换成分
     * @var array
     */
    protected $yuan2fens = [];
    /**
     * 这个数组中的字段会被认为是json字符串,并且被json_decode
     * @var array
     */
    protected $json2arrays = [];
    /**
     * 这个数组中的key会被替换成value
     * 实际字段 => 系统处理字段
     * @var array
     */
    protected $map = [];

    public function __construct(string $content)
    {
        $data = json_decode(urldecode($content), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg());
        }

        foreach ($data as $key => $datum) {
            if (array_key_exists($key,$this->map)) {
                $key = $this->map[$key];
            }
            if (property_exists($this, $key)) {
                if (in_array($key, $this->dates)) {
                    if (is_integer($datum)) {
                        $this->$key = Carbon::createFromTimestamp($datum);
                    }else{
                        $this->$key = new Carbon($datum);
                    }
                }elseif(in_array($key, $this->json2arrays)){
                    $this->$key = json_decode($datum, true);
                }elseif(in_array($key, $this->yuan2fens)){
                    $this->$key = intval($datum*100);
                }else{
                    $this->$key = $datum;
                }
            }
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }
}
