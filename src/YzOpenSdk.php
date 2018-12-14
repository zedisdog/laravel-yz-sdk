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
use Illuminate\Config\Repository;
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
     * @var array
     */
    protected $dont_report = [
        140400200,
        135500009
    ];

    /**
     * YzOpenSdk constructor.
     * @param Application $app
     * @param string|null $access_token
     * @param string|null $refresh_token
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
     * 设置不抛出异常的错误码
     * @param int $code
     */
    public function dontReport(int $code)
    {
        $this->dont_report[] = $code;
        $this->dont_report = array_unique($this->dont_report);
    }

    /**
     * 获取access_token,如果没有就去获取或者刷新access_token
     * @return string
     * @throws \Exception
     */
    public function getToken(): string
    {
        /**
         * @var Repository $config
         */
        $config = $this->app['config'];
        /**
         * @var Request $request
         */
        $request = $this->app->make('request');
        //有access_token 就返回access_token
        if ($this->access_token && !$request->has('code')) {
            return $this->access_token;
        }else{
            if ($config->get('yz.multi_seller')) {
                $keys['redirect_uri'] = \URL::Route($config->get('yz.callback'));
            } else {
                $keys['kdt_id'] = $config->get('yz.kdt_id');
            }

            if ($config->get('yz.multi_seller')) {
                // 如果有code就去获取，没有就尝试通过refresh_token刷新access_token
                if ($request->has('code')) {
                    $type = 'oauth';
                    $keys['code'] = $request->input('code');
                }else{
                    $type = 'refresh_token';
                    $keys['refresh_token'] = $this->refresh_token;
                }

                if (
                    empty($keys['code']) && empty($keys['refresh_token'])
                ) {
                    throw new \RuntimeException('no code or refresh_token');
                }
            } else {
                $type = 'self';
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
            $result = (new \Youzan\Open\Token($config->get('yz.client_id'), $config->get('yz.client_secret')))->getToken($type, $keys);
            $this->origin_data = $result;
            if (!isset($result['access_token'])) {
                $context = [
                    'result' => $result,
                    'type' => $type,
                    'keys' => $keys
                ];
                Log::error('no access_token', $context);
            }
            if (!empty($result['access_token'])) {
                $this->access_token = $result['access_token'];
                if ($config->get('yz.multi_seller')) {
                    $this->refresh_token = $result['refresh_token'];
                }

                /**
                 * @var CacheManager $cache
                 */
                $cache = $this->app->make('cache');
                if ($config->get('yz.multi_seller')) {
                    if (!$this->seller_id) {
                        $client = new Client($this->access_token);
                        $info = $this->checkError($client->post('youzan.shop.get', '3.0.0', []));

                        $logger = $this->app->make('log');
                        $logger->info('yz_api_call', ['method' => 'youzan.shop.get','params' => [],'response_field' => 'response', 'result' => $info]);

                        $info = array_get($info, 'response');
                        $this->seller_id = $info['id'];
                    }
                    if ($cache->getDefaultDriver() == 'redis') {
                        $cache->tags('yz_seller_' . $this->seller_id)->put('access_token', $this->access_token, $result['expires_in']/60);
                        $cache->tags('yz_seller_' . $this->seller_id)->put('refresh_token', $this->refresh_token, 60 * 24 * 28);
                    } else {
                        $cache->put('yz_seller_' . $this->seller_id . '_access_token', $this->access_token, $result['expires_in']/60);
                        $cache->put('yz_seller_' . $this->seller_id . '_refresh_token', $this->refresh_token, 60 * 24 * 28);
                    }
                } else {
                    $cache->put('yz_access_token', $this->access_token, $result['expires_in']/60);
                }

                return $result['access_token'];
            } else {
                return $this->access_token;
            }
        }
    }

    /**
     * 向用户添加tag
     * @param int|string $id openid或者fans_id
     * @param $tags
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function addTags($id, string $tags, $version='3.0.0'): ?array
    {
        $method = 'youzan.users.weixin.follower.tags.add';

        $params = [
            'tags' => $tags
        ];

        if (is_string($id) && preg_match('/[a-zA-Z]/',$id)) {
            $params['weixin_openid'] = $id;
        } else {
            $params['fans_id'] = $id;
        }

        return $this->post($method, $version, $params, 'response.user');
    }

    /**
     * 获取单个商品信息
     * @param int $product_id
     * @param string $version
     * @return array|null
     * @throws \Exception
     * @deprecated 1.2.3 will remove in version 2
     */
    public function getProduct(int $product_id, string $version='3.0.0'): ?array
    {
        $method = 'youzan.item.get';

        $params = [
            'item_id' => $product_id,
        ];

        return $this->post($method, $version, $params, 'response.item');
    }

    /**
     * 通过 open_id 或者 fans_id 获取用户信息
     * @param integer|string $id fans_id或者open_id
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getFollower($id, string $version='3.0.0'): ?array
    {
        $method = 'youzan.users.weixin.follower.get';

        if (is_string($id) && preg_match('/[a-zA-Z]/',$id)) {
            $params['weixin_openid'] = $id;
        } else {
            $params['fans_id'] = $id;
        }

        return $this->post($method, $version, $params, 'response.user');
    }

    /**
     * 根据手机号码获取openId
     * @param string $phone
     * @param string $version
     * @return null|string
     * @throws \Exception
     */
    public function getOpenId(string $phone, string $version='3.0.0'): ?string
    {
        $method = 'youzan.user.weixin.openid.get';

        $result = $this->post($method, $version, ['mobile' => $phone]);

        return $result['open_id'];
    }

    /**
     * 根据交易号获取分销员手机号
     * @param string $out_trade_id
     * @param string $version
     * @return null|string
     * @throws \Exception
     */
    public function getPhoneByTrade(string $out_trade_id, $version='3.0.0'): ?string
    {
        $method = 'youzan.salesman.trades.account.get';
        $params = [
            'order_no' => $out_trade_id
        ];
        $result = $this->post($method, $version, $params);
        return $result['mobile'];
    }

    /**
     * 获取店铺信息
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getShopInfo(string $version = '3.0.0'): ?array
    {
        return $this->getUserInfo($version);
    }
    /**
     * 获取店铺信息
     * @param string $version
     * @return array|null
     * @throws \Exception
     * @deprecated 1.0.0 will remove in version 2
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
        $result = $this->post($method, $version);

        return $result;
    }

    /**
     * 清除token
     * @param int|null $seller_id
     */
    public static function destroy(?int $seller_id = null)
    {
        /**
         * @var CacheManager $cache
         */
        $cache = app()->make('cache');
        if ($seller_id) {
            if ($cache->getDefaultDriver() == 'redis') {
                $cache->tags('yz_seller_' . $seller_id)->forget('access_token');
                $cache->tags('yz_seller_' . $seller_id)->forget('refresh_token');
            } else {
                $cache->forget('yz_seller_' . $seller_id . '_access_token');
                $cache->forget('yz_seller_' . $seller_id . '_refresh_token');
            }
        } else {
            $cache->forget('yz_access_token');
            $cache->forget('yz_refresh_token');
        }
    }

    /**
     * 获取商品类目列表
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getItemCategories(string $version='3.0.0'): ?array
    {
        return $this->getType($version);
    }
    /**
     * 获取商品类目列表
     * @param string $version
     * @return array|null
     * @throws \Exception
     * @deprecated 1.0.0 will remove in version 2
     */
    public function getType(string $version='3.0.0'): ?array
    {
        $method = 'youzan.itemcategories.get';

        return $this->post($method, $version, [], 'response.categories');
    }

    /**
     * 获取在销售的商品
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getOnSaleItems(array $params = ['page_size' => 300], string $version = '3.0.0'): ?array
    {
        $method = 'youzan.items.onsale.get';

        return $this->post($method, $version, $params, 'response.items');
    }

    /**
     * 获取仓库中的商品
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getInventoryItems(array $params = ['page_size' => 300], string $version = '3.0.0'): ?array
    {
        $method = 'youzan.items.inventory.get';
        return $this->post($method, $version, $params, 'response.items');
    }

    /**
     * 获取所有商品，包括上架的和仓库中的
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getProducts(array $params = ['page_size' => 300], string $version='3.0.0'): ?array
    {
        if (isset($params['banner'])) {
            throw new \InvalidArgumentException('method getProduct is not supported param banner');
        }
        return array_merge($this->getInventoryItems($params, $version), $this->getOnSaleItems($params, $version));
    }

    /**
     * 获取店铺基础信息
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getShopBaseInfo($version = '3.0.0'): ?array
    {
        return $this->getUserBasicInfo($version);
    }

    /**
     * 获取店铺基础信息
     * @param string $version
     * @return array|null
     * @throws \Exception
     * @deprecated 1.0.0 will remove in version 2
     */
    public function getUserBasicInfo($version = '3.0.0'): ?array
    {
        $method = 'youzan.shop.basic.get';

        return $this->post($method, $version);
    }

    /**
     * @param string $method
     * @param string $version
     * @param string $response_field
     * @return mixed
     * @throws \Exception
     */
    private function get(string $method, string $version, $response_field = 'response')
    {
        $client = new Client($this->getToken());
        $result = $this->checkError($client->get($method, $version));

        return array_get($result, $response_field);
    }

    /**
     * 获取交易信息
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

        switch ($version) {
            case '3.0.0':
                $response_field = 'response.trade';
                break;
            case '4.0.0':
                $response_field = 'response';
                break;
            default:
                throw new \InvalidArgumentException('unknown version ' . $version);
        }

        return $this->post($method, $version, $my_params, $response_field);
    }

    /**
     * 向用户发送赠品
     * @param string $activity_id 赠品活动id
     * @param string $fans_id 粉丝id或有赞id
     * @param string $version 版本
     * @return array|null
     * @throws \Exception
     * todo: add param buyer_id in version 2
     */
    public function givePresent(string $activity_id, string $fans_id, $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.present.give';

        $params = [
            'activity_id' => $activity_id
        ];

        $params['fans_id'] = $fans_id;

        return $this->post($method, $version, $params);
    }

    /**
     * 是否存在token
     * @param $seller_id
     * @return bool
     */
    public function hasToken($seller_id = null): bool
    {
        /**
         * @var Repository $config
         */
        $config = $this->app['config'];
        if (!$seller_id) {
            $seller_id = $this->seller_id;
        }
        /**
         * @var CacheManager $cache
         */
        $cache = $this->app->make('cache');
        if ($config->get('yz.multi_seller')) {
            if ($cache->getDefaultDriver() == 'redis') {
                return $cache->tags('yz_seller_' . $seller_id)->has('refresh_token');
            } else {
                return $cache->has('yz_seller_' . $seller_id . '_refresh_token');
            }
        } else {
            return $cache->has('yz_refresh_token');
        }
    }

    /**
     * @param int $points
     * @param string $id mobile or fans_id
     * @param bool $isOpenUserId
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function pointIncrease(int $points, string $id, bool $isOpenUserId = false, string $version='3.0.1'): bool
    {
        $method = 'youzan.crm.customer.points.increase';

        $params = [
            'points' => $points
        ];

        if ($isOpenUserId) {
            $params['open_user_id'] = $id;
        } else if (preg_match('/^1[3-9]\d{9}$/',$id)) {
            $params['mobile'] = $id;
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

    public function setSellerId($seller_id)
    {
        $this->seller_id = $seller_id;
        $this->tryTokenCache();
        return $this;
    }

    private function tryTokenCache()
    {
        /**
         * @var Repository $config
         */
        $config = $this->app['config'];
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
        if ($config->get('yz.multi_seller') && $this->seller_id) {
            if ($cache->getDefaultDriver() == 'redis') {
                $this->access_token = $cache->tags('yz_seller_' . $this->seller_id)->get('access_token');
                $this->refresh_token = $cache->tags('yz_seller_' . $this->seller_id)->get('refresh_token');
            } else {
                $this->access_token = $cache->get('yz_seller_' . $this->seller_id . '_access_token');
                $this->refresh_token = $cache->get('yz_seller_' . $this->seller_id . '_refresh_token');
            }
        } else {
            $this->access_token = $cache->get('yz_access_token');
            $this->refresh_token = $cache->get('yz_refresh_token');
        }
    }

    /**
     * 检查返回消息是否是错误消息
     * 如果是错误消息, 抛出异常
     * @param array $result
     * @return array|null
     */
    private function checkError(array $result): ?array
    {
        if (isset($result['error_response'])) {
            if (in_array($result['error_response']['code'], $this->dont_report)) {
                return null;
            } else {
                throw new \RuntimeException(json_encode($result));
            }
        } else {
            return $result;
        }
    }

    /**
     * @param string $method
     * @param string $version
     * @param array $params
     * @param string $response_field
     * @param array $files
     * @return array|null
     * @throws \Exception
     */
    protected function post(string $method, string $version, array $params = [], string $response_field = 'response', array $files = [])
    {
        $client = new Client($this->getToken());
        $result = $this->checkError($client->post($method, $version, $params, $files));

        $logger = $this->app->make('log');
        $logger->info('yz_api_call', ['method' => $method,'params' => $params,'response_field' => $response_field, 'result' => $result]);

        return $result ? array_get($result, $response_field) : $result;
    }

    /**
     * 获取进行中的赠品
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

        return $this->post($method, $version, $params, 'response.presents');
    }

    /**
     * 获取未结束的优惠活动
     * @param array $fields
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getUnfinishedCoupons(array $fields = [], string $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.coupons.unfinished.search';
        if ($fields) {
            $params['fields'] = $fields;
        } else {
            $params = [];
        }
        return $this->post($method, $version, $params, 'response.coupons');
    }

    /**
     * 获取优惠券详情
     * @param $id
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function getCoupon($id, string $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.coupon.detail.get';

        $params['id'] = $id;

        return $this->post($method, $version, $params);
    }

    /**
     * 发放优惠券/码
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     * todo: enhance params in version 2
     */
    public function takeCoupon(array $params, string $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.coupon.take';
        return $this->post($method, $version, $params);
    }

    /**
     * （分页查询）查询优惠券（码）活动列表
     * todo: 返回一个分页对象以供查询,这个对象可以迭代
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     * todo: enhance params in version 2
     */
    public function getCouponList(array $params = [], string $version = '3.0.0'): ?array
    {
        $method = 'youzan.ump.coupon.search';
        $params = array_merge(['page_no' => 1, 'page_size' => 1000], $params);
        return $this->post($method, $version, $params, 'response.groups');
    }

    /**
     * 获取分销员信息
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     * todo: enhance params in version 2
     */
    public function getSalesman(array $params = [], string $version = '3.0.0'): ?array
    {
        $method = 'youzan.salesman.account.get';
        return $this->post($method, $version, $params);
    }

    /**
     * 获取分销员列表
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     * todo: enhance params in version 2
     */
    public function getSalesmanList(array $params = [], string $version = '3.0.0'): ?array
    {
        $method = 'youzan.salesman.accounts.get';
        return $this->post($method, $version, $params);
    }

    /**
     * 创建商品
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function itemCreate(array $params, string $version = '3.0.0'): ?array
    {
        if (empty($params['desc']) || empty($params['image_ids']) || empty($params['price']) || empty($params['title'])) {
            throw new \LogicException('fields [desc],[image_ids],[price],[title] are required');
        }
        $method = 'youzan.item.create';
        return $this->post($method, $version, $params, 'response.item');
    }

    /**
     * 删除商品
     * @param $item_id
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function itemDelete($item_id, string $version = '3.0.0'): bool
    {
        $method = 'youzan.item.delete';
        $result = $this->post($method, $version, ['item_id' => $item_id]);

        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 更新商品
     * @param array $params
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function itemUpdate(array $params, string $version = '3.0.0'): bool
    {
        if (empty($params['item_id'])) {
            throw new \LogicException('item_id is required');
        }
        $method = 'youzan.item.update';
        $result = $this->post($method, $version, $params, 'response');
        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 获取商品
     * @param $params
     * @param string $version
     * @return array|null
     * @throws \Exception
     * todo: enhance params in version 2
     */
    public function itemGet(array $params, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.item.get';
        return $this->post($method, $version, $params, 'response.item');
    }

    /**
     * 上架商品
     * @param $item_id
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function itemUpdateListing($item_id, string $version = '3.0.0'): bool
    {
        $method = 'youzan.item.update.listing';
        $result = $this->post($method, $version, ['item_id' => $item_id]);
        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 下架商品
     * @param $item_id
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function itemUpdateDelisting($item_id, string $version = '3.0.0'): bool
    {
        $method = 'youzan.item.update.delisting';
        $result = $this->post($method, $version, ['item_id' => $item_id]);
        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 更新sku
     * @param $params
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function skuUpdate(array $params, string $version = '3.0.0'): bool
    {
        if (empty($params['item_id']) || empty($params['sku_id'])) {
            throw new \LogicException('item_id and sku_id are required');
        }
        $method = 'youzan.item.sku.update';
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 获取sku详情
     * @param $item_id
     * @param $sku_id
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function skuGet($item_id, $sku_id, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.item.sku.get';
        $params = [
            'item_id' => $item_id
        ];
        if ($sku_id) {
            $params['sku_id'] = $sku_id;
        }
        return $this->post($method, $version, $params);
    }

    /**
     * 上传图片
     * @param array $files
     * @param string $version
     * @return array|null
     * @throws \Exception
     */
    public function imageUpload(array $files, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.materials.storage.platform.img.upload';
        return $this->post($method, $version, [], 'response', $files);
    }

    /**
     * 主动退款
     * @param string $desc
     * @param $oid
     * @param $refund_fee
     * @param string $tid
     * @param string $version
     * @return array|null
     * @throws \Exception
     * todo: enhance param refund_fee in version 2
     */
    public function tradeRefund(string $desc, string $oid, string $refund_fee, string $tid, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.trade.refund.seller.active';
        $params = compact('desc', 'oid', 'refund_fee', 'tid');
        return $this->post($method, $version, $params);
    }

    /**
     * 同意退款
     * @param string $refund_id
     * @param string $r_version
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function tradeRefundAgree(string $refund_id, string $r_version, string $version = '3.0.0'): bool
    {
        $method = 'youzan.trade.refund.agree';
        $params = [
            'refund_id' => $refund_id,
            'version' => $r_version
        ];
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 拒绝退款
     * @param string $refund_id
     * @param string $remark
     * @param string $r_version
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function tradeRefundRefuse(string $refund_id, string $remark, string $r_version, string $version = '3.0.0'): bool
    {
        $method = 'youzan.trade.refund.refuse';
        $params = [
            'refund_id' => $refund_id,
            'remark' => $remark,
            'version' => $r_version
        ];
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result['is_success'];
        } else {
            return false;
        }
    }

    /**
     * 外部电子卡券创建核销码
     * @param string $tickets
     * @param string $orderNo
     * @param int $singleNum
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function ticketCreate(string $tickets, string $orderNo, int $singleNum = 1, string $version = '1.0.0'): bool
    {
        $method = 'youzan.ebiz.external.ticket.create';
        $params = compact('tickets', 'orderNo', 'singleNum');
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result['boolean'];
        } else {
            return false;
        }
    }

    /**
     * 外部电子卡券核销
     * @param array $params
     * @param string $version
     * @return bool
     * @throws \Exception
     */
    public function ticketVerify(array $params, $version = '1.0.0'): bool
    {
        if (empty($params['tickets']) || empty($params['orderNo'])) {
            throw new \LogicException('fields [tickets],[orderNo] are required');
        }
        $method = 'youzan.ebiz.external.ticket.verify';
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result['boolean'];
        } else {
            return false;
        }
    }
}
