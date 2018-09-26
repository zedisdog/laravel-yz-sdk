<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-9-26
 * Time: 下午4:42
 */

namespace Dezsidog\YzSdk\Test;


use Dezsidog\YzSdk\YzOpenSdk;
use PHPUnit\Framework\TestCase;

class SdkTest extends \Orchestra\Testbench\TestCase
{
    protected $app_id = '55fa2f69ae80d0f84d';
    protected $app_secret = '3dbb4f48d9b5f71e9ce01487b767c117';

    protected function getPackageProviders($app)
    {
        return ['Dezsidog\YzSdk\YzSdkServiceProvider'];
    }

    public function testItemCreate()
    {
        $this->app['config']->set('yz.multi_seller', false);
        $sdk = $this->app->make(YzOpenSdk::class);
    }
}