<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-4
 * Time: 下午6:24
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk;


use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Youzan\Open\Client;

class YzOpenSdk
{
    /**
     * 有赞的access_token
     * @var string
     */
    protected $access_token;
    /**
     * access_token的刷新token
     * @var string
     */
    protected $refresh_token;
    /**
     * @var array
     */
    public $origin_data;

    /**
     * @var Application
     */
    public $app;

    /**
     * @var string
     */
    public $seller_id;

    /**
     * YzOpenSdk constructor.
     * @param Application $app
     * @param null|string $access_token
     * @param null|string $refresh_token
     */
    public function __construct($app, ?string $access_token = null, ?string $refresh_token = null)
    {
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->app = $app;
        if (!$this->access_token && !$this->refresh_token) {
            $this->tryTokenCache();
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getToken(): string
    {
        //有access_token 就返回access_token
        if ($this->access_token) {
            return $this->access_token;
        }else{
            /**
             * todo: 这里没有确定
             */
            $keys['redirect_uri'] = \URL::to(config('yz.callback'));

            // 如果有refresh_token就直接刷新
            // 没有refresh_token就跳转授权
            if ($this->refresh_token) {
                $type = 'refresh_token';
                $keys['refresh_token'] = $this->refresh_token;
            }else{
                $request = $this->app->make('request');
                $type = 'oauth';
                $keys['code'] = $request->input('code');
            }

            if (
                empty($keys['code']) && empty($keys['refresh_token'])
            ) {
                throw new \RuntimeException('no code or refresh_token');
            }

            /*
             * [
                "access_token" => "9b7c7f882f033afa9bfc7070a0951f60" (一般是7天)
                "expires_in" => 604800
                "refresh_token" => "aab315a848dd34088512fc536ffe6593" (一般是28天)
                "scope" => "multi_store shop item trade logistics coupon_advanced user pay_qrcode trade_virtual reviews item_category storage retail_goods"
                "token_type" => "Bearer"
                ]
             */
            $result = (new \Youzan\Open\Token(config('youzan.client_id'), config('youzan.client_secret')))->getToken($type, $keys);

            $this->origin_data = $result;
            $this->access_token = $result['access_token'];
            $this->refresh_token = $result['refresh_token'];

            /**
             * @var CacheManager $cache
             */
            $cache = $this->app->make('cache');
            if ($this->seller_id) {
                $cache->tags('yz_seller_' . $this->seller_id)->put('access_token', $this->access_token, $result['expires_in']/60);
                $cache->tags('yz_seller_' . $this->seller_id)->put('refresh_token', $this->refresh_token, 60 * 24 * 28);
            } else {
                $cache->put('yz_access_token', $this->access_token, $result['expires_in']/60);
                $cache->put('yz_refresh_token', $this->refresh_token, 60 * 24 * 28);
            }

            return $result['access_token'];
        }
    }

    /**
     * @param int $product_id
     * @return array|null
     * @throws \Exception
     */
    public function getProduct(int $product_id): ?array
    {
        $method = 'youzan.item.get';
        $api_version = '3.0.0';

        $params = [
            'item_id' => $product_id,
        ];

        $client = new Client($this->getToken());
        $result = $client->post($method, $api_version, $params);
        if (isset($result['response'])) {
            return $result['response']['item'];
        }else{
            return null;
        }
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getUserInfo(): ?array
    {
        $method = 'youzan.shop.get';
        $api_version = '3.0.0';

        /*
         * [
              "response" => [
                "id" => "40199820"
                "name" => "有直销测试"
                "logo" => "https://img.yzcdn.cn/public_files/2016/05/13/8f9c442de8666f82abaf7dd71574e997.png"
                "intro" => ""
              ]
            ]
         */
        $client = new Client($this->getToken());
        $result = $client->post($method, $api_version);
        if (isset($result['response'])) {
            return $result['response'];
        }else{
            return null;
        }
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getType(): ?array
    {
        $method = 'youzan.itemcategories.get';
        $api_version = '3.0.0';

        $client = new Client($this->getToken());
        $result = $client->post($method, $api_version);
        if (isset($result['response']) && isset($result['response']['categories'])) {
            return $result['response']['categories'];
        }else{
            return null;
        }
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getProducts(): ?array
    {
        $methods = [
            [
                'method' => 'youzan.items.onsale.get',
                'api_version' => '3.0.0'
            ],
            [
                'method' => 'youzan.items.inventory.get',
                'api_version' => '3.0.0'
            ],
        ];

        $products = [];
        $client = new Client($this->getToken());

        $params = [
            'page_size' => 300
        ];

        foreach ($methods as $method) {
            $result = $client->post($method['method'],$method['api_version'], $params);
            if (!empty($result['response']['items'])) {
                $products = array_merge($products,$result['response']['items']);
            }
        }

        return $products;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getUserBasicInfo(): ?array
    {
        $method = 'youzan.shop.basic.get';
        $api_version = '3.0.0';

        $client = new Client($this->getToken());
        $result = $client->get($method, $api_version);

        return $result['response'];
    }

    /**
     * @param string $trade_id
     * @return array|null
     * @throws \Exception
     */
    public function getTrade(string $trade_id): ?array
    {
        $method = 'youzan.trade.get'; //要调用的api名称
        $api_version = '3.0.0'; //要调用的api版本号

        $my_params = [
            'tid' => $trade_id,
        ];

        $client = new Client($this->getToken());
        $result = $client->post($method, $api_version, $my_params);

        return $result['response']['trade'];
    }

    public function setSellerId(string $seller_id)
    {
        $this->seller_id = $seller_id;
        $this->tryTokenCache();
    }

    private function tryTokenCache()
    {
        /**
         * @var Application $app
         * @var Request $request
         * @var CacheManager $cache
         */
        $request = $app->make('request');
        $cache = $app->make('cache');

        if ($request->has('kdt_id')) {
            $this->seller_id = $request->input('kdt_id');
        }

        // 先尝试取之前的yz token
        if ($this->seller_id) {
            $this->access_token = $cache->tags('yz_seller_' . $this->seller_id)->get('access_token');
            $this->refresh_token = $cache->tags('yz_seller_' . $this->seller_id)->get('refresh_token');
        } else {
            $this->access_token = $cache->get('yz_access_token');
            $this->refresh_token = $cache->get('yz_refresh_token');
        }
    }
}
