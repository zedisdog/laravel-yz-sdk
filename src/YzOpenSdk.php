<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-1-4
 * Time: 下午6:24
 */
declare(strict_types=1);
namespace Dezsidog\YzSdk;


use Dezsidog\YzSdk\Bridge\LaravelCache;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Old\Open\Client;
use Old\Open\Token as YzToken;

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
     * @var CacheInterface
     */
    protected $cache;
    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var UrlGenerator
     */
    protected $url_generator;
    /**
     * @var YzToken
     */
    protected $yz_token;
    /**
     * @var LogManager
     */
    protected $log;

    /**
     * @var array
     */
    protected $dont_report = [
        140400200,
        135500009
    ];

    protected $dont_report_all = false;

    /**
     * YzOpenSdk constructor.
     * @param Repository $config
     * @param ServerRequestInterface|\Symfony\Component\HttpFoundation\Request|Request $request
     * @param YzToken $yz_token
     * @param UrlGenerator $url_generator
     * @param CacheInterface $cache
     * @param LogManager $log
     * @param string|null $access_token
     * @param string|null $refresh_token
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct(
        Repository $config,
        $request,
        YzToken $yz_token,
        UrlGenerator $url_generator,
        CacheInterface $cache,
        LogManager $log,
        ?string $access_token = null,
        ?string $refresh_token = null
    )
    {
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->cache = $cache;
        $this->config = $config;
        $this->setRequest($request);
        $this->request = $request;
        $this->url_generator = $url_generator;
        $this->yz_token = $yz_token;
        $this->log = $log;
        if (!$this->access_token && !$this->refresh_token) {
            $this->tryTokenCache();
        }
    }

    public function dontReportAll()
    {
        $this->dont_report_all = true;
        return $this;
    }
    protected function getSellerId()
    {
        return $this->seller_id;
    }

    protected function setRequest($request)
    {
        if ($request instanceof ServerRequestInterface) {
            $factory = new HttpFoundationFactory();
            $this->request = Request::createFromBase($factory->createRequest($request));
        } elseif ($request instanceof \Symfony\Component\HttpFoundation\Request) {
            $this->request = Request::createFromBase($request);
        } else {
            $this->request = $request;
        }
    }

    public function getAccessToken()
    {
        return $this->access_token;
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

    protected function getRefreshToken()
    {
        return $this->refresh_token;
    }

    protected function buildTypeAndKeys()
    {
        if ($this->config->get('yz.multi_seller')) {
            $keys['redirect_uri'] = $this->url_generator->route($this->config->get('yz.callback'));
        } else {
            $keys['kdt_id'] = $this->config->get('yz.kdt_id');
        }

        if ($this->config->get('yz.multi_seller')) {
            // 如果有code就去获取，没有就尝试通过refresh_token刷新access_token
            if ($this->request->has('code')) {
                $type = 'oauth';
                $keys['code'] = $this->request->input('code');
            }else{
                $type = 'refresh_token';
                $keys['refresh_token'] = $this->getRefreshToken();
            }

            if (
                empty($keys['code']) && empty($keys['refresh_token'])
            ) {
                throw new \RuntimeException('no code or refresh_token');
            }
        } else {
            $type = 'self';
        }

        return [$type, $keys];
    }

    /**
     * 获取access_token,如果没有就去获取或者刷新access_token
     * @return string
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(): string
    {
        /**
         * @var Repository $config
         */
        $config = $this->config;
        /**
         * @var Request $request
         */
        $request = $this->request;
        //有access_token 就返回access_token
        if ($this->getAccessToken() && !$request->has('code')) {
            return $this->getAccessToken();
        }else{
            [$type, $keys] = $this->buildTypeAndKeys();

            /*
             * [
                "access_token" => "9b7c7f882f033afa9bfc7070a0951f60" (一般是7天)
                "expires_in" => 604800
                "refresh_token" => "aab315a848dd34088512fc536ffe6593" (一般是28天)
                "scope" => "multi_store shop item trade logistics coupon_advanced user pay_qrcode trade_virtual reviews item_category storage retail_goods"
                "token_type" => "Bearer"
                ]
             */
//            $result = (new \Youzan\Open\Token($config->get('yz.client_id'), $config->get('yz.client_secret')))->getToken($type, $keys);
            $result = $this->yz_token->getToken($type, $keys);
            $this->origin_data = $result;

            // 检查是否取到了access_token
            $this->checkAccessToken($type, $keys);

            if (!empty($this->origin_data['access_token'])) {
                $cache = $this->cache;
                $this->access_token = $this->origin_data['access_token'];
                if ($config->get('yz.multi_seller')) {
                    $this->seller_id = $this->discoverySellerId();
                    $cache->set('yz_seller_' . $this->getSellerId() . '_refresh_token', $this->refresh_token, 60 * 24 * 28);
                } else {
                    $this->seller_id = $keys['kdt_id'];
                }

                $cache->set('yz_seller_' . $this->getSellerId() . '_access_token', $this->access_token, $this->origin_data['expires_in']/60);

                return $this->origin_data['access_token'];
            } else {
                return $this->access_token;
            }
        }
    }

    /**
     * 获取商家id
     * @throws \Exception
     */
    protected function discoverySellerId()
    {
        $this->refresh_token = $this->origin_data['refresh_token'];

        $client = new Client($this->access_token);
        $info = $this->checkError($client->post('youzan.shop.get', '3.0.0', []));

        $logger = $this->log;
        $logger->info('yz_api_call', ['method' => 'youzan.shop.get','params' => [],'response_field' => 'response', 'result' => $info]);

        $info = array_get($info, 'response');
        return $info['id'];
    }

    /**
     * 向用户添加tag
     * @param int|string $id openid或者fans_id
     * @param string $tags
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * 通过 open_id 或者 fans_id 获取用户信息
     * @param integer|string $id fans_id或者open_id
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getShopInfo(string $version = '3.0.0'): ?array
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
     * @param int|string $seller_id
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function destroy($seller_id = null): void
    {
        $this->cache->deleteMultiple([
            'yz_seller_' . $seller_id . '_access_token',
            'yz_seller_' . $seller_id . '_access_token'
        ]);
    }

    /**
     * 获取商品类目列表
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getItemCategories(string $version='3.0.0'): ?array
    {
        $method = 'youzan.itemcategories.get';

        return $this->post($method, $version, [], 'response.categories');
    }

    /**
     * 获取在销售的商品
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getShopBaseInfo($version = '3.0.0'): ?array
    {
        $method = 'youzan.shop.basic.get';

        return $this->post($method, $version);
    }

    /**
     * 获取交易信息
     * @param string $trade_id
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @param integer|string $identification fans_id或buyer_id
     * @param bool $is_buyer_id 是否是buyer_id
     * @param string $version 版本
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function givePresent(string $activity_id, $identification, $is_buyer_id = false, $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.present.give';

        $params = [
            'activity_id' => $activity_id
        ];

        if ($is_buyer_id) {
            $params['buyer_id'] = $identification;
        } else {
            $params['fans_id'] = $identification;
        }

        return $this->post($method, $version, $params);
    }

    /**
     * 是否存在token
     * @param $seller_id
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function hasToken($seller_id = null): bool
    {
        if (!$seller_id) {
            $seller_id = $this->getSellerId();
        }

        return $this->cache->has('yz_seller_'.$seller_id.'_refresh_token');
    }

    /**
     * @param int $points
     * @param string $id mobile or fans_id
     * @param bool $is_open_user_id
     * @param string $version
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function pointIncrease(int $points, string $id, bool $is_open_user_id = false, string $version='3.0.1'): bool
    {
        $method = 'youzan.crm.customer.points.increase';

        $params = [
            'points' => $points
        ];

        if ($is_open_user_id) {
            $params['open_user_id'] = $id;
        } else if (is_string($id) && preg_match('/^1[3-9]\d{9}$/',$id)) {
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

    /**
     * @param $seller_id
     * @return $this
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setSellerId($seller_id)
    {
        $this->seller_id = $seller_id;
        $this->tryTokenCache();
        return $this;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function tryTokenCache()
    {
        /**
         * @var Request $request
         * @var LaravelCache $cache
         */
        $request = $this->request;
        $cache = $this->cache;

        if ($request->has('kdt_id')) {
            $this->seller_id = $request->input('kdt_id');
        }

        if ($this->getSellerId()) {
            $this->access_token = $cache->get('yz_seller_' . $this->getSellerId().'_access_token');
            $this->refresh_token = $cache->get('yz_seller_' . $this->getSellerId().'_refresh_token');
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
            if ($this->dont_report_all || in_array($result['error_response']['code'], $this->dont_report)) {
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
     * @return array|bool|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    protected function post(string $method, string $version, array $params = [], string $response_field = 'response', array $files = [])
    {
        $logger = $this->log;
        $client = new Client($this->getToken());
        $logger->info('yz_api_params', ['method' => $method,'params' => $params,'response_field' => $response_field]);
        $result = $client->post($method, $version, $params, $files);
        $logger->info('yz_api_call', ['method' => $method,'params' => $params,'response_field' => $response_field, 'result' => $result]);
        $result = $this->checkError($result);

        return $result ? array_get($result, $response_field) : $result;
    }

    /**
     * 获取进行中的赠品
     * @param array $fields
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getCoupon($id, string $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.coupon.detail.get';

        $params['id'] = $id;

        return $this->post($method, $version, $params);
    }

    /**
     * 发放优惠券/码
     * @param integer|string $coupon_group_id 优惠券码组id
     * @param integer|string $identification fans_id|mobile|open_user_id|weixin_openid
     * @param bool $is_open_user_id
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function takeCoupon($coupon_group_id, $identification, $is_open_user_id = false, string $version='3.0.0'): ?array
    {
        $method = 'youzan.ump.coupon.take';
        $params['coupon_group_id'] = $coupon_group_id;
        if ($is_open_user_id) {
            $params['open_user_id'] = $identification;
        } elseif (is_string($identification) && preg_match('/^1[3-9]\d{9}$/',$identification)) {
            $params['mobile'] = $identification;
        } elseif (is_string($identification) && preg_match('/[a-zA-Z]/', $identification)) {
            $params['weixin_openid'] = $identification;
        } else {
            $params['fans_id'] = $identification;
        }

        return $this->post($method, $version, $params);
    }

    /**
     * （分页查询）查询优惠券（码）活动列表
     * @param string $group_type 活动类型 PROMOCARD 优惠券，PROMOCODE 优惠码
     * @param string $status 活动状态 FUTURE 未开始 ,END 已结束,ON 进行中 （默认查所有状态）
     * @param int $page_size 每页数量
     * @param int $page_no 第几页
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     * todo: 返回一个分页对象以供查询,这个对象可以迭代
     */
    public function getCouponList(string $group_type = '', string $status = '', int $page_no = 1, int $page_size = 1000, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.ump.coupon.search';

        $group_type = strtoupper($group_type);
        $status = strtoupper($status);

        $group_type_options = [
            '',
            'PROMOCARD',
            'PROMOCODE'
        ];

        if (!in_array($group_type, $group_type_options)) {
            throw new \Exception('params [group_type]('. $group_type .') most be one of [PROMOCARD,PROMOCODE]');
        }

        $status_options = [
            '',
            'FUTURE',
            'END',
            'ON'
        ];

        if (!in_array($status, $status_options)) {
            throw new \Exception('params [status]('. $status .') most be one of [FUTURE,END,ON]');
        }

        $params = [
            'page_no' => $page_no,
            'page_size' => $page_size
        ];
        if ($status) {
            $params['status'] = $status;
        }
        if ($group_type) {
            $params['group_type'] = $group_type;
        }

        return $this->post($method, $version, $params, 'response.groups');
    }

    /**
     * 获取分销员信息
     * @param integer|string $identification fans_id|mobile
     * @param int $fans_type 粉丝类型 默认1
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getSalesman($identification, int $fans_type = 1, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.salesman.account.get';
        $params = [
            'fans_type' => $fans_type
        ];
        if (is_string($identification) && preg_match('/^1[3-9]\d{9}$/', $identification)) {
            $params['mobile'] = $identification;
            $params['fans_id'] = 0;
        } else {
            $params['fans_id'] = $identification;
            $params['mobile'] = '0';
        }
        return $this->post($method, $version, $params);
    }

    /**
     * 获取分销员列表
     * @param int $page_no
     * @param int $page_size
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getSalesmanList($page_no = 1, $page_size = 100, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.salesman.accounts.get';
        $params = [
            'page_no' => $page_no,
            'page_size' => $page_size
        ];
        return $this->post($method, $version, $params);
    }

    /**
     * 创建商品
     * @param array $params
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @param integer|string $identification 标识
     * @param bool $alias 是否是别名
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function itemGet($identification, $alias = false, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.item.get';
        if ($alias) {
            $params = [
                'alias' => $identification
            ];
        } else {
            $params = [
                'item_id' => $identification
            ];
        }
        return $this->post($method, $version, $params, 'response.item');
    }

    /**
     * 上架商品
     * @param $item_id
     * @param string $version
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @param array $params
     * @param string $version
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function imageUpload(array $files, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.materials.storage.platform.img.upload';
        return $this->post($method, $version, [], 'response', $files);
    }

    /**
     * 主动退款
     * @param string $desc
     * @param string $oid
     * @param int $refund_fee 退款金额，单位分
     * @param string $tid
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function tradeRefund(string $desc, string $oid, int $refund_fee, string $tid, string $version = '3.0.0'): ?array
    {
        $method = 'youzan.trade.refund.seller.active';
        $params = compact('desc', 'oid', 'tid');
        $params['refund_fee'] = sprintf('%.2f', $refund_fee/100.00);
        return $this->post($method, $version, $params);
    }

    /**
     * 同意退款
     * @param string $refund_id
     * @param string $r_version
     * @param string $version
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function ticketCreate(string $tickets, string $orderNo, int $singleNum = 1, string $version = '1.0.0'): bool
    {
        $method = 'youzan.ebiz.external.ticket.create';
        $params = compact('tickets', 'orderNo', 'singleNum');
        /**
         * @var bool $result
         */
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 外部电子卡券核销
     * @param array $params
     * @param string $version
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function ticketVerify(array $params, $version = '1.0.0'): bool
    {
        if (empty($params['tickets']) || empty($params['orderNo'])) {
            throw new \LogicException('fields [tickets],[orderNo] are required');
        }
        $method = 'youzan.ebiz.external.ticket.verify';
        /**
         * @var bool $result
         */
        $result = $this->post($method, $version, $params);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    protected function checkAccessToken($type, $keys)
    {
        if (!isset($this->origin_data['access_token'])) {
            $context = [
                'result' => $this->origin_data,
                'type' => $type,
                'keys' => $keys
            ];
            $this->log->error('request access_token failed', $context);
        }
    }

    /**
     * @param int $page_no 当前页
     * @param int $page_size 每月显示条数
     * @param int|null $show_sold_out
     * @param string $keyword 搜索关键字
     * @param array $tag_ids 标签id列表
     * @param array $item_ids 商品id列表
     * @param string $version 接口版本
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function itemList(
        int $page_no = 1,
        int $page_size = 100,
        ?int $show_sold_out = null,
        string $keyword = '',
        array $tag_ids = [],
        array $item_ids = [],
        string $version = '3.0.0'
    )
    {
        $method = 'youzan.item.search';
        $params = ['page_no' => $page_no, 'page_size' => $page_size];
        if ($show_sold_out !== null) {
            $params['show_sold_out'] = $show_sold_out;
        }
        if ($keyword) {
            $params['q'] = $keyword;
        }
        if (!empty($tag_ids)) {
            $params['tag_ids'] = implode(',', $tag_ids);
        }
        if (!empty($item_ids)) {
            $params['item_ids'] = implode(',', $item_ids);
        }

        return $this->post($method, $version, $params);
    }

    /**
     * @param string $keyword
     * @param int $page_no
     * @param int $page_size
     * @param int|null $show_sold_out
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function itemSearch(
        string $keyword,
        int $page_no = 1,
        int $page_size = 100,
        ?int $show_sold_out = null,
        $version = '3.0.0'
    )
    {
        return $this->itemList($page_no, $page_size, $show_sold_out, $keyword, [], [], $version);
    }

    /**
     * @param array $item_ids
     * @param int $page_no
     * @param int $page_size
     * @param int|null $show_sold_out
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function itemListByItemIds(
        array $item_ids,
        int $page_no = 1,
        int $page_size = 100,
        ?int $show_sold_out = null,
        $version = '3.0.0'
    )
    {
        return $this->itemList($page_no, $page_size, $show_sold_out, '', [], $item_ids, $version);
    }

    /**
     * @param array $tag_ids
     * @param int $page_no
     * @param int $page_size
     * @param int|null $show_sold_out
     * @param string $version
     * @return array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function itemListByTagIds(
        array $tag_ids,
        int $page_no = 1,
        int $page_size = 100,
        ?int $show_sold_out = null,
        $version = '3.0.0'
    )
    {
        return $this->itemList($page_no, $page_size, $show_sold_out, '', $tag_ids, [], $version);
    }
}
