<?php
/**
 * Created by zed.
 */

namespace Dezsidog\YzSdk\Test;


use Dezsidog\YzSdk\YzOpenSdk;
use Mockery\Mock;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $app_id = '55fa2f69ae80d0f84d';
    protected $app_secret = '3dbb4f48d9b5f71e9ce01487b767c117';
    protected $kdt_id = '40151071';
    /**
     * @var YzOpenSdk
     */
    protected $sdk;

    public function setUp()
    {
        parent::setUp();
        $this->app['config']->set('yz.multi_seller', false);
        $this->app['config']->set('yz.client_id', $this->app_id);
        $this->app['config']->set('yz.client_secret', $this->app_secret);
        $this->app['config']->set('yz.kdt_id', $this->kdt_id);
    }

    protected function mockSdk(array $methods = [])
    {
        $methods = implode(',', $methods);
        return \Mockery::mock(YzOpenSdk::class."[{$methods}]", [$this->app])->shouldAllowMockingProtectedMethods();
    }

    protected function getPackageProviders($app)
    {
        return ['Dezsidog\YzSdk\YzSdkServiceProvider'];
    }

    public function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }
}