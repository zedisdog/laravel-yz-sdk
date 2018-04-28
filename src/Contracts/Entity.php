<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-25
 * Time: 下午2:05
 */
declare(strict_types=1);

namespace Dezsidog\YzSdk\Contracts;


interface Entity
{
    public function __construct(string $content);
}
