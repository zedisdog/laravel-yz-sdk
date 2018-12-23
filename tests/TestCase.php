<?php
/**
 * Created by zed.
 */

namespace Dezsidog\YzSdk\Test;


use Dezsidog\YzSdk\Bridge\LaravelCache;
use Dezsidog\YzSdk\YzOpenSdk;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Routing\UrlGenerator;
use Mockery\Mock;
use Mockery\MockInterface;
use Youzan\Open\Token;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var YzOpenSdk
     */
    protected $sdk;
    /**
     * @var MockInterface|Application
     */
    protected $app;
    /**
     * @var LaravelCache|MockInterface
     */
    protected $cache;
    /**
     * @var Repository|MockInterface
     */
    protected $config;
    /**
     * @var Request|MockInterface
     */
    protected $request;
    /**
     * @var UrlGenerator|MockInterface
     */
    protected $url_generator;
    /**
     * @var MockInterface|Token
     */
    protected $yz_token;
    /**
     * @var MockInterface|LogManager
     */
    protected $log;

    public function setUp()
    {
        parent::setUp();
        $this->config = \Mockery::mock(Repository::class)->shouldAllowMockingProtectedMethods();
        $this->config->shouldReceive('has')->andReturn(false);
        $this->config->shouldReceive('get', ['yz.multi_seller'])->andReturn(true);

        $this->app = \Mockery::mock(Application::class)->shouldAllowMockingProtectedMethods();
        $this->app->shouldReceive('offsetGet', ['config'])->andReturn($this->config);
        $this->app->shouldReceive('make', ['config'])->andReturn($this->config);

        $this->cache = \Mockery::mock(LaravelCache::class)->shouldAllowMockingProtectedMethods();
        $this->cache->shouldReceive('get')->andReturn(null);

        $this->request = \Mockery::mock(Request::class);
        $this->request->shouldReceive('has')->andReturn(false);

        $this->url_generator = \Mockery::mock(UrlGenerator::class);
        $this->url_generator->shouldReceive('route')->andReturn('http://test_url.com');

        $this->yz_token = \Mockery::mock(Token::class);
        $this->yz_token->shouldReceive('getToken')->with('self')->andReturn([
            'access_token' => 'test_access_token', // String 是 可用于调用API的 access_token
            'expires_in' => 604800, // Number 是 access_token 的有效时长,单位：秒（过期时间：7天）
            'scope' => 'multi_store shop item trade logistics coupon_advanced user pay_qrcode trade_virtual reviews item_category storage retail_goods'
        ]);
        $this->yz_token->shouldReceive('getToken')->andReturn([
            "access_token" => "test_access_token", // (一般是7天)
            "expires_in" => 604800,
            "refresh_token" => "test_refresh_token", // (一般是28天)
            "scope" => "multi_store shop item trade logistics coupon_advanced user pay_qrcode trade_virtual reviews item_category storage retail_goods",
            "token_type" => "Bearer"
        ]);

        $this->log = \Mockery::mock(LogManager::class);
        $this->log->shouldReceive('info')->andReturn(true);
    }

    protected function mockSdk(array $methods = [], $params = [])
    {
        $methods = implode(',', $methods);

        $params = array_merge([
            'config' => $this->config,
            'request' => $this->request,
            'yz_token' => $this->yz_token,
            'urlGenerator' => $this->url_generator,
            'cache' => $this->cache,
            'log' => $this->log
        ], $params);

        $sdk = \Mockery::mock(YzOpenSdk::class."[{$methods}]", array_values($params))->shouldAllowMockingProtectedMethods();

        return $sdk;
    }

    public function tearDown()
    {
        parent::tearDown();
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->Mockery_getExpectationCount());
        }
        \Mockery::close();
    }
}