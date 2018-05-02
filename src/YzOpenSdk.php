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
use Illuminate\Support\Facades\Log;
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
            $result = (new \Youzan\Open\Token(config('yz.client_id'), config('yz.client_secret')))->getToken($type, $keys);

            $this->origin_data = $result;
            if (!isset($result['access_token'])) {
                $context = [
                    'result' => $result,
                    'type' => $type,
                    'keys' => $keys
                ];
                Log::error('no access_token', $context);
            }
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
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getProduct(int $product_id, $version='3.0.0'): ?array
    {
        $method = 'youzan.item.get';

        $params = [
            'item_id' => $product_id,
        ];

        return $this->post($method, $version, $params);
    }

    /**
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getUserInfo($version = '3.0.0'): ?array
    {
        $method = 'youzan.shop.get';

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
        return $this->post($method, $version);
    }

    /**
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getType(string $version='3.0.0'): ?array
    {
        $method = 'youzan.itemcategories.get';

        $result = $this->post($method, $version);

        if (isset($result) && isset($result['categories'])) {
            return $result['categories'];
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
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getUserBasicInfo($version = '3.0.0'): ?array
    {
        $method = 'youzan.shop.basic.get';

        return $this->get($method, $version);
    }

    /**
     * @param string $method
     * @param string $version
     * @return mixed
     * @throws \Exception
     */
    private function get(string $method, string $version)
    {
        $client = new Client($this->getToken());
        $result = $this->checkError($client->get($method, $version));

        return $result['response'];
    }

    /**
     * @param string $trade_id
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getTrade(string $trade_id, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.trade.get'; //要调用的api名称

        $my_params = [
            'tid' => $trade_id,
        ];

        return $this->post($method, $version, $my_params)['trade'];
    }

    /**
     * 向用户发送赠品
     * @param string $activity_id 赠品活动id
     * @param string $id 粉丝id或有赞id
     * @param string $version 版本
     * @return array|null
     * @throws \Exception
     */
    public function givePresent(string $activity_id,string $id, $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.present.give';

        $params = [
            'activity_id' => $activity_id
        ];

        if (strlen($id) == 28) {
            $params['fans_id'] = $id;
        } else {
            $params['buyer_id'] = $id;
        }

        return $this->post($method, $version, $params);
    }

    /**
     * @param int $points
     * @param string $id
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function pointIncrease(int $points, string $id, string $version='3.0.1')
    {
        $method = 'youzan.crm.customer.points.increase';

        $params = [
            'points' => $points
        ];

        if (strlen($id) == 11) {
            $params['mobile'] = $id;
        }elseif (strlen($id) == 28) {
            $params['open_user_id'] = $id;
        } else {
            $params['fans_id'] = $id;
        }

        $result = $this->post($method, $version, $params);

        switch ($result['is_success']) {
            case 'true':
                return true;
            case 'false':
                return false;
            default:
                return false;
        }
    }

    public function setSellerId(string $seller_id)
    {
        $this->seller_id = $seller_id;
        $this->tryTokenCache();
    }

    private function tryTokenCache()
    {
        /**
         * @var Request $request
         * @var CacheManager $cache
         */
        $request = $this->app->make('request');
        $cache = $this->app->make('cache');

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

    /**
     * 检查返回消息是否是错误消息
     * 如果时错误消息, 抛出异常
     * @param array $result
     * @return array|null
     */
    private function checkError(array $result): ?array
    {
        if (isset($result['error_response'])) {
            throw new \RuntimeException(json_encode($result));
        }

        return $result;
    }

    /**
     * @param string $method
     * @param string $version
     * @param array $params
     * @return array|null
     * @throws \Exception
     */
    private function post(string $method, string $version, array $params=[])
    {
        $client = new Client($this->getToken());
        $result = $this->checkError($client->post($method, $version, $params));

        return $result['response'];
    }

    /**
     * 获取赠品
     * @param array $fields
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getPresents(array $fields = [], string $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.presents.ongoing.all';
        $params = [];
        if (!empty($fields)) {
            $params['fields'] = implode(',', $fields);
        }

        return $this->post($method, $version, $params)['presents'];
    }
}
