<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-9-26
 * Time: 下午4:42
 */

namespace Dezsidog\YzSdk\Test;

class SdkTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testImageUpload()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "kdt_id" => $this->kdt_id,
            "image_url" => "http://img.yzcdn.cn/upload_files/2017/06/08/FhJ5xShUa7ac0Ptamhg6La164JV_.jpg",
            "image_id" => 14537
        ]);
        $param = [
            [
                'url' => __DIR__ . '/img/product.jpg',
                'field' => 'image[]',
            ]
        ];
        $result = $sdk->imageUpload($param);
        $this->assertEquals($this->kdt_id, $result['kdt_id']);
        $this->assertNotEmpty($result['image_url']);
        $this->assertNotEmpty($result['image_id']);
    }

    /**
     * @throws \Exception
     */
    public function testItemCreate()
    {
        $sdk = $this->mockSdk([
            'post'
        ]);
        $sdk->shouldReceive('post')->andReturn([
            'template' => [
                'template_title' => '普通版',
                'template_id' => 0,
            ],
            'auto_listing_time' => '1970-01-01 08:00:00',
            'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y584nimrxbj3&from=wsc&kdtfrom=wsc',
            'skus' => [
                [
                    'sku_unique_code' => '13876843316',
                    'with_hold_quantity' => 0,
                    'item_id' => 1387684,
                    'created' => '1970-01-18:15:48:57',
                    'price' => 10000,
                    'stock_num' => 100,
                    'properties_name_json' => '[{"vid":1217,"v":"绿色","kid":1,"k":"颜色"},{"vid":1367,"v":"l","kid":2,"k":"尺寸"},{"vid":303435,"v":"1024G","kid":41,"k":"内存"}]',
                    'modified' => '1970-01-18:15:48:57',
                    'sku_id' => 3316,
                ],
                [
                    'sku_unique_code' => '13876843317',
                    'with_hold_quantity' => 0,
                    'item_id' => 1387684,
                    'created' => '1970-01-18:15:48:57',
                    'price' => 10000,
                    'stock_num' => 100,
                    'properties_name_json' => '[{"vid":1217,"v":"绿色","kid":1,"k":"颜色"},{"vid":1367,"v":"l","kid":2,"k":"尺寸"},{"vid":6356,"v":"16G","kid":41,"k":"内存"}]',
                    'modified' => '1970-01-18:15:48:57',
                    'sku_id' => 3317,
                ],
            ],
            'post_fee' => 0,
            'virtual_extend' => [
                'effective_type' => 0,
                'holidays_available' => true,
            ],
            'buy_quota' => 0,
            'item_type' => 61,
            'title' => 'test product',
            'join_level_discount' => false,
            'item_no' => '',
            'kdt_id' => 191,
            'purchase_right' => false,
            'price' => 10000,
            'presale_extend' => [],
            'alias' => '1y584nimrxbj3',
            'post_type' => 1,
            'quantity' => 200,
            'item_tags' => [],
            'item_id' => 1387684,
            'item_imgs' => [
                [
                    'thumbnail' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                    'created' => '2017-06-08 23:50:04',
                    'medium' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    'id' => 1,
                    'url' => 'https://img.yzcdn.cn/upload_files/no_pic.png',
                    'combine' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                ],
            ],
            'fenxiao_extend' => [],
            'is_listing' => false,
            'sold_num' => 0,
            'hotel_extend' => [],
            'delivery_template_info' => [],
            'share_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y584nimrxbj3&from=wsc&kdtfrom=wsc',
            'pic_thumb_url' => 'https://img.yzcdn.cn/upload_files/no_pic.png!120x120.jpg',
            'is_lock' => false,
            'pic_url' => 'https://img.yzcdn.cn/upload_files/no_pic.png',
            'desc' => 'test product',
            'cid' => 8000001,
        ]);
        $testProduct = [
            'desc' => 'test product',
            'image_ids' => 1,
            'price' => 1,
            'title' => 'test product'
        ];
        $result = $sdk->itemCreate($testProduct);
        $this->assertEquals($result['desc'], 'test product');
        $this->assertEquals($result['title'], 'test product');
        $this->assertEquals(1, $result['item_imgs'][0]['id']);
    }

    public function testDelete()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "item_id" => 1387684,
            "is_success" => true
        ]);
        $result = $sdk->itemDelete(1387684);
        $this->assertTrue($result);
    }

    public function testUpdateDelisting()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "item_id" => 1387684,
            "is_success" => true
        ]);
        $result = $sdk->itemUpdateDelisting(1387684);
        $this->assertTrue($result);
    }

    public function testUpdateListing()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "item_id" => 1387684,
            "is_success" => true
        ]);
        $result = $sdk->itemUpdateListing(1387684);
        $this->assertTrue($result);
    }

    public function testItemUpdate()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "item_id" => 1387684,
            "is_success" => true
        ]);
        $testProduct['desc'] = 'test product2';
        $testProduct['title'] = 'test product2';
        $testProduct['item_id'] = 1387684;
        $result = $sdk->itemUpdate($testProduct);
        $this->assertTrue($result);
    }

    public function testItemGet()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'template' => [
                'template_title' => '普通版',
                'template_id' => 0,
            ],
            'auto_listing_time' => '1970-01-01 08:00:00',
            'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y584nimrxbj3&from=wsc&kdtfrom=wsc',
            'skus' => [
                [
                    'sku_unique_code' => '13876843316',
                    'with_hold_quantity' => 0,
                    'item_id' => 1387684,
                    'created' => '1970-01-18:15:48:57',
                    'price' => 10000,
                    'stock_num' => 100,
                    'properties_name_json' => '[{"vid":1217,"v":"绿色","kid":1,"k":"颜色"},{"vid":1367,"v":"l","kid":2,"k":"尺寸"},{"vid":303435,"v":"1024G","kid":41,"k":"内存"}]',
                    'modified' => '1970-01-18:15:48:57',
                    'sku_id' => 3316,
                ],
                [
                    'sku_unique_code' => '13876843317',
                    'with_hold_quantity' => 0,
                    'item_id' => 1387684,
                    'created' => '1970-01-18:15:48:57',
                    'price' => 10000,
                    'stock_num' => 100,
                    'properties_name_json' => '[{"vid":1217,"v":"绿色","kid":1,"k":"颜色"},{"vid":1367,"v":"l","kid":2,"k":"尺寸"},{"vid":6356,"v":"16G","kid":41,"k":"内存"}]',
                    'modified' => '1970-01-18:15:48:57',
                    'sku_id' => 3317,
                ],
            ],
            'post_fee' => 0,
            'virtual_extend' => [
                'effective_type' => 0,
                'holidays_available' => true,
            ],
            'buy_quota' => 0,
            'item_type' => 61,
            'title' => 'test product',
            'join_level_discount' => false,
            'item_no' => '',
            'kdt_id' => 191,
            'purchase_right' => false,
            'price' => 10000,
            'presale_extend' => [],
            'alias' => '1y584nimrxbj3',
            'post_type' => 1,
            'quantity' => 200,
            'item_tags' => [],
            'item_id' => 1387684,
            'item_imgs' => [
                [
                    'thumbnail' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                    'created' => '2017-06-08 23:50:04',
                    'medium' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    'id' => 1,
                    'url' => 'https://img.yzcdn.cn/upload_files/no_pic.png',
                    'combine' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                ],
            ],
            'fenxiao_extend' => [],
            'is_listing' => false,
            'sold_num' => 0,
            'hotel_extend' => [],
            'delivery_template_info' => [],
            'share_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y584nimrxbj3&from=wsc&kdtfrom=wsc',
            'pic_thumb_url' => 'https://img.yzcdn.cn/upload_files/no_pic.png!120x120.jpg',
            'is_lock' => false,
            'pic_url' => 'https://img.yzcdn.cn/upload_files/no_pic.png',
            'desc' => 'test product',
            'cid' => 8000001,
        ]);
        $result = $sdk->itemGet(['item_id' => 1387684]);
        $this->assertEquals($result['desc'], 'test product');
        $this->assertEquals($result['title'], 'test product');
    }

    /**
     * @throws \Exception
     */
    public function testGetSku()
    {
        $sdk = $this->mockSdk(['post']);
        $item_id = 446427206;
        $sku_id = 36269029;
        $sdk->shouldReceive('post')->andReturn([
            'properties_name' => '497:220:净含量:300g',
            'sku_unique_code' => '44642720636269029',
            'quantity' => 98,
            'item_id' => $item_id,
            'created' => '2018-12-11 14:46:34',
            'properties_name_json' => '[{"k":"净含量","kid":497,"v":"300g","vid":220}]',
            'num_iid' => $item_id,
            'sku_id' => $sku_id,
            'outer_id' => '',
            'item_no' => '',
            'with_hold_quantity' => 0,
            'price' => '0.01',
            'modified' => '2018-12-11 14:47:38',
        ]);
        $result = $sdk->skuGet($item_id, $sku_id);
        $this->assertEquals($item_id, $result['item_id']);
    }

    public function testUpdateSku()
    {
        $sdk = $this->mockSdk(['post']);
        $item_id = 446427206;
        $sku_id = 36269029;
        $sdk->shouldReceive('post')->andReturn([
            "is_success" => true
        ]);
        $result = $sdk->skuUpdate([
            'item_id' => $item_id,
            'sku_id' => $sku_id,
            'quantity' => 4
        ]);
        $this->assertTrue($result);
    }

    /**
     * @throws \Exception
     */
    public function testRefund()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "is_success" => true,
            "refund_id" => "201804032029140000020485"
        ]);
        $result = $sdk->tradeRefund('测试退款', '1474409360913335275', '0.01', 'E20180929163409008800001');
        $this->assertTrue($result['is_success']);
        $this->assertNotEmpty($result['refund_id']);
    }

    public function testAddTags()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'sex' => 'm',
            'tags' => [
                [
                    'id' => 4034378,
                    'name' => 'test',
                ],
            ],
            'is_follow' => true,
            'points' => 22,
            'traded_num' => 12,
            'traded_money' => '25.08',
            'level_info' => [],
            'user_id' => 4851134360,
            'weixin_openid' => 'oaePdw8j7mWPM-tEJ13T7xFPQ2Oc',
            'nick' => 'z🤡',
            'avatar' => 'http://thirdwx.qlogo.cn/mmopen/6khpuIqCOnib7llpGz3RHt7hBep7tLpIuUXqNg536zrc4J8xLww5T9ibhicWRvmDQcOUIO59icxsSfIfU650YhGL5kXNWsYnbicoA/132',
            'follow_time' => 1543992612,
            'province' => '四川',
            'city' => '成都',
            'union_id' => 'o9MYF1kED4TAUxn6GuDWNGqTIUE8',
        ]);
        $result = $sdk->addTags(4851134360, 'test');
        $this->assertEquals(4851134360, $result['user_id']);
        $sdk->shouldHaveReceived('post', [
            'youzan.users.weixin.follower.tags.add',
            '3.0.0',
            [
                'tags' => 'test',
                'fans_id' => 4851134360
            ],
            'response.user'
        ]);
        // 测试字符串数字
        $sdk->addTags('4851134360', 'test');
        $sdk->shouldHaveReceived('post', [
            'youzan.users.weixin.follower.tags.add',
            '3.0.0',
            [
                'tags' => 'test',
                'fans_id' => '4851134360'
            ],
            'response.user'
        ]);
        // 测试包含字母的字符串
        $sdk->addTags('485113a4360', 'test');
        $sdk->shouldHaveReceived('post', [
            'youzan.users.weixin.follower.tags.add',
            '3.0.0',
            [
                'tags' => 'test',
                'weixin_openid' => '485113a4360'
            ],
            'response.user'
        ]);
    }

    public function testGetProduct()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'template' => [
                'template_title' => '普通版',
                'template_id' => 0,
            ],
            'auto_listing_time' => '1970-01-01 08:00:00',
            'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y584nimrxbj3&from=wsc&kdtfrom=wsc',
            'skus' => [
                [
                    'sku_unique_code' => '13876843316',
                    'with_hold_quantity' => 0,
                    'item_id' => 1387684,
                    'created' => '1970-01-18:15:48:57',
                    'price' => 10000,
                    'stock_num' => 100,
                    'properties_name_json' => '[{"vid":1217,"v":"绿色","kid":1,"k":"颜色"},{"vid":1367,"v":"l","kid":2,"k":"尺寸"},{"vid":303435,"v":"1024G","kid":41,"k":"内存"}]',
                    'modified' => '1970-01-18:15:48:57',
                    'sku_id' => 3316,
                ],
                [
                    'sku_unique_code' => '13876843317',
                    'with_hold_quantity' => 0,
                    'item_id' => 1387684,
                    'created' => '1970-01-18:15:48:57',
                    'price' => 10000,
                    'stock_num' => 100,
                    'properties_name_json' => '[{"vid":1217,"v":"绿色","kid":1,"k":"颜色"},{"vid":1367,"v":"l","kid":2,"k":"尺寸"},{"vid":6356,"v":"16G","kid":41,"k":"内存"}]',
                    'modified' => '1970-01-18:15:48:57',
                    'sku_id' => 3317,
                ],
            ],
            'post_fee' => 0,
            'virtual_extend' => [
                'effective_type' => 0,
                'holidays_available' => true,
            ],
            'buy_quota' => 0,
            'item_type' => 61,
            'title' => 'test product',
            'join_level_discount' => false,
            'item_no' => '',
            'kdt_id' => 191,
            'purchase_right' => false,
            'price' => 10000,
            'presale_extend' => [],
            'alias' => '1y584nimrxbj3',
            'post_type' => 1,
            'quantity' => 200,
            'item_tags' => [],
            'item_id' => 1387684,
            'item_imgs' => [
                [
                    'thumbnail' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                    'created' => '2017-06-08 23:50:04',
                    'medium' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    'id' => 1,
                    'url' => 'https://img.yzcdn.cn/upload_files/no_pic.png',
                    'combine' => 'https://img.yzcdn.cn/upload_files/no_pic.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                ],
            ],
            'fenxiao_extend' => [],
            'is_listing' => false,
            'sold_num' => 0,
            'hotel_extend' => [],
            'delivery_template_info' => [],
            'share_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y584nimrxbj3&from=wsc&kdtfrom=wsc',
            'pic_thumb_url' => 'https://img.yzcdn.cn/upload_files/no_pic.png!120x120.jpg',
            'is_lock' => false,
            'pic_url' => 'https://img.yzcdn.cn/upload_files/no_pic.png',
            'desc' => 'test product',
            'cid' => 8000001,
        ]);
        $result = $sdk->getProduct(1387684);
        $this->assertEquals($result['desc'], 'test product');
        $this->assertEquals($result['title'], 'test product');
    }

    public function testGetFollower()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'is_follow' => true,
            'city' => '成都',
            'sex' => 'm',
            'avatar' => 'http://thirdwx.qlogo.cn/mmopen/6khpuIqCOnib7llpGz3RHt7hBep7tLpIuUXqNg536zrc4J8xLww5T9ibhicWRvmDQcOUIO59icxsSfIfU650YhGL5kXNWsYnbicoA/132',
            'traded_num' => 12,
            'points' => 22,
            'tags' => [
                [
                    'name' => 'test',
                    'id' => 4034378,
                ],
            ],
            'nick' => 'z🤡',
            'follow_time' => 1543992612,
            'province' => '四川',
            'user_id' => 4851134360,
            'union_id' => 'o9MYF1kED4TAUxn6GuDWNGqTIUE8',
            'level_info' => [],
            'traded_money' => '25.08',
            'weixin_openid' => 'oaePdw8j7mWPM-tEJ13T7xFPQ2Oc',
        ]);
        $result = $sdk->getFollower(4851134360);
        $this->assertEquals(4851134360, $result['user_id']);
        $sdk->shouldHaveReceived('post', [
            'youzan.users.weixin.follower.get',
            '3.0.0',
            [
                'fans_id' => 4851134360
            ],
            'response.user'
        ]);

        $sdk->getFollower('4851134360');
        $sdk->shouldHaveReceived('post', [
            'youzan.users.weixin.follower.get',
            '3.0.0',
            [
                'fans_id' => '4851134360'
            ],
            'response.user'
        ]);

        $sdk->getFollower('485113436a0');
        $sdk->shouldHaveReceived('post', [
            'youzan.users.weixin.follower.get',
            '3.0.0',
            [
                'weixin_openid' => '485113436a0'
            ],
            'response.user'
        ]);
    }

    public function testGetOpenId()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "open_id" => "oTtVis-xiMQjlBME5Xi4Bc_twuqA",
            "union_id" => "oqY0-wpXFmBsPI2IrTUYx3DigfjY"
        ]);
        $result = $sdk->getOpenId('15281009123');
        $this->assertEquals('oTtVis-xiMQjlBME5Xi4Bc_twuqA', $result);
    }

    public function testGetPhoneByTrade()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "mobile" => "18628100512"
        ]);
        $result = $sdk->getPhoneByTrade('E20181211162239017400014');
        $this->assertEquals('18628100512', $result);
    }

    public function testGetShopInfo()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "id" => "18898637",
            "name" => "卡门测试内部专用店铺",
            "logo" => "https://img.yzcdn.cn/public_files/2016/05/13/8f9c442de8666f82abaf7dd71574e997.png",
            "intro" => ""
        ]);
        $result = $sdk->getShopInfo();
        $this->assertEquals('18898637', $result['id']);
    }

    public function testGetItemCategories()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            [
                'cid' => 1000000,
                'parent_cid' => 0,
                'name' => '女人',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 1000000,
                        'parent_cid' => 1000000,
                        'name' => '女人',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 2000000,
                'parent_cid' => 0,
                'name' => '男人',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 2000000,
                        'parent_cid' => 2000000,
                        'name' => '男人',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 3000000,
                'parent_cid' => 0,
                'name' => '食品',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 3000000,
                        'parent_cid' => 3000000,
                        'name' => '食品',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 4000000,
                'parent_cid' => 0,
                'name' => '美妆',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 4000000,
                        'parent_cid' => 4000000,
                        'name' => '美妆',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 5000000,
                'parent_cid' => 0,
                'name' => '亲子',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 5000000,
                        'parent_cid' => 5000000,
                        'name' => '亲子',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 6000000,
                'parent_cid' => 0,
                'name' => '居家',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 6000000,
                        'parent_cid' => 6000000,
                        'name' => '居家',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 7000000,
                'parent_cid' => 0,
                'name' => '数码家电',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 7000000,
                        'parent_cid' => 7000000,
                        'name' => '数码家电',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
            [
                'cid' => 8000000,
                'parent_cid' => 0,
                'name' => '其他',
                'is_parent' => true,
                'sub_categories' => [
                    [
                        'cid' => 8000001,
                        'parent_cid' => 8000000,
                        'name' => '礼品鲜花',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000002,
                        'parent_cid' => 8000000,
                        'name' => '餐饮外卖',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000003,
                        'parent_cid' => 8000000,
                        'name' => '丽人健身',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000004,
                        'parent_cid' => 8000000,
                        'name' => '休闲娱乐',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000005,
                        'parent_cid' => 8000000,
                        'name' => '酒店客栈',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000006,
                        'parent_cid' => 8000000,
                        'name' => '婚庆摄影',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000007,
                        'parent_cid' => 8000000,
                        'name' => '汽车养护',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000008,
                        'parent_cid' => 8000000,
                        'name' => '家政服务',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000009,
                        'parent_cid' => 8000000,
                        'name' => '门票卡券',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000010,
                        'parent_cid' => 8000000,
                        'name' => '家装建材',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000011,
                        'parent_cid' => 8000000,
                        'name' => '钟表眼镜',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000012,
                        'parent_cid' => 8000000,
                        'name' => '宠物',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000013,
                        'parent_cid' => 8000000,
                        'name' => '文化收藏',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000014,
                        'parent_cid' => 8000000,
                        'name' => '日用百货',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000015,
                        'parent_cid' => 8000000,
                        'name' => '教育培训',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000016,
                        'parent_cid' => 8000000,
                        'name' => '媒体服务',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000018,
                        'parent_cid' => 8000000,
                        'name' => '充值缴费',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                    [
                        'cid' => 8000017,
                        'parent_cid' => 8000000,
                        'name' => '其他',
                        'is_parent' => false,
                        'sub_categories' => NULL,
                    ],
                ],
            ],
        ]);
        $result = $sdk->getItemCategories();
        $this->assertNotEmpty($result);
        $this->assertCount(8, $result);
    }

    public function testGetOnSaleItems()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            [
                'created_time' => '2016-07-18 15:18:00',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=27bbv813gfcwf',
                'quantity' => 1111,
                'post_fee' => 0,
                'item_id' => 87823,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290977,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290980,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290984,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290993,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290996,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'Haier/海尔BCD-160TMPQ冰箱小型双门式 家用双开门 两门节能冰箱',
                'item_no' => '',
                'update_time' => '2016-07-18 15:18:33',
                'price' => 105900,
                'alias' => '27bbv813gfcwf',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 29,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateZJ',
                ],
            ],
            [
                'created_time' => '2016-07-26 14:02:02',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=272of885gb0lr',
                'quantity' => 99,
                'post_fee' => 1,
                'item_id' => 104270,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal2016072614020224397',
                'item_no' => '',
                'update_time' => '2016-07-26 14:02:02',
                'price' => 100,
                'alias' => '272of885gb0lr',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-07-26 14:02:25',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y7mke8ktca33',
                'quantity' => 99,
                'post_fee' => 1,
                'item_id' => 104272,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal2016072614022545260',
                'item_no' => '',
                'update_time' => '2016-07-26 14:02:25',
                'price' => 100,
                'alias' => '1y7mke8ktca33',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-07-21 16:44:26',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2odyltuod88tr',
                'quantity' => 1905,
                'post_fee' => 0,
                'item_id' => 95211,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033917,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033920,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033923,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033926,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033933,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '【丑羽】狂神耐打羽毛球  12只装耐打王羽毛球 一个顶两ymq羽毛球',
                'item_no' => '',
                'update_time' => '2016-09-12 11:04:46',
                'price' => 1100,
                'alias' => '2odyltuod88tr',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 11:55:37',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nlghuiefl3bj',
                'quantity' => 10000,
                'post_fee' => 0,
                'item_id' => 345,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291155361',
                'item_no' => '',
                'update_time' => '2016-04-29 11:55:37',
                'price' => 1,
                'alias' => '3nlghuiefl3bj',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 11:57:04',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=366hm5dkf8xm7',
                'quantity' => 10000,
                'post_fee' => 0,
                'item_id' => 347,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291157030',
                'item_no' => '',
                'update_time' => '2016-04-29 11:57:04',
                'price' => 1,
                'alias' => '366hm5dkf8xm7',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 12:02:04',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2g3wc88z6h30f',
                'quantity' => 100,
                'post_fee' => 0,
                'item_id' => 352,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291202045',
                'item_no' => '',
                'update_time' => '2017-01-09 14:25:35',
                'price' => 1,
                'alias' => '2g3wc88z6h30f',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 32,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateZJ',
                ],
            ],
            [
                'created_time' => '2016-04-29 11:57:29',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=272pz6ig1nxr3',
                'quantity' => 10000,
                'post_fee' => 0,
                'item_id' => 349,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291157284',
                'item_no' => '',
                'update_time' => '2016-04-29 11:57:29',
                'price' => 1,
                'alias' => '272pz6ig1nxr3',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 11:57:29',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1ydujozuyzh3z',
                'quantity' => 1000,
                'post_fee' => 0,
                'item_id' => 350,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291157297',
                'item_no' => '',
                'update_time' => '2016-04-29 11:57:29',
                'price' => 1,
                'alias' => '1ydujozuyzh3z',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 31,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateZJ',
                ],
            ],
            [
                'created_time' => '2016-06-01 17:55:55',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2x5blpxeqf5fz',
                'quantity' => 234,
                'post_fee' => 0,
                'item_id' => 15657,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503943,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503945,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503948,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503951,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503954,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'suguangrong普通商品3',
                'item_no' => '',
                'update_time' => '2016-06-30 19:49:13',
                'price' => 1200,
                'alias' => '2x5blpxeqf5fz',
                'post_type' => 1,
                'delivery_template' => [],
            ],
        ]);
        $result = $sdk->getOnSaleItems();
        $this->assertNotEmpty($result);
        $this->assertCount(10, $result);
        $sdk->shouldHaveReceived('post', [
            'youzan.items.onsale.get',
            '3.0.0',
            ['page_size' => 300],
            'response.items'
        ]);
    }

    public function testGetInventoryItems()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            [
                'created_time' => '2016-06-29 10:24:37',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3f0alqhw1qx8v',
                'quantity' => 1999987,
                'post_fee' => 1,
                'item_id' => 45401,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 228,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '分销猫',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 2,
                'alias' => '3f0alqhw1qx8v',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 3232,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '山猫测试',
                ],
            ],
            [
                'created_time' => '2016-08-28 15:53:20',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nrnf9nfkf5wf',
                'quantity' => 109,
                'post_fee' => 0,
                'item_id' => 219758,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 278,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'suguangrong测试4',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 300,
                'alias' => '3nrnf9nfkf5wf',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-08-28 15:53:41',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=36dwwa287g6fj',
                'quantity' => 999960,
                'post_fee' => 0,
                'item_id' => 219783,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 9,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'ly新分销商品',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 20000,
                'alias' => '36dwwa287g6fj',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-11-18 17:10:33',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y3y20svfv667',
                'post_fee' => 0,
                'item_id' => 460396,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2029,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '商品啊',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '1y3y20svfv667',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-11-21 16:29:14',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3f5971z5xt6kv',
                'post_fee' => 0,
                'item_id' => 465155,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '3f5971z5xt6kv',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-11-18 17:10:28',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2g1dqvegvoar3',
                'post_fee' => 0,
                'item_id' => 460394,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2029,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '商品啊111',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '2g1dqvegvoar3',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-12-28 10:21:01',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2fss76ht4cklb',
                'quantity' => 123,
                'post_fee' => 0,
                'item_id' => 588593,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '2fss76ht4cklb',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 58621,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateTest',
                ],
            ],
            [
                'created_time' => '2016-12-28 10:50:14',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y3yxdefikm8f',
                'quantity' => 200,
                'post_fee' => 0,
                'item_id' => 588614,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest有规格分销商品',
                'item_no' => 'ADFA',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 99,
                'alias' => '1y3yxdefikm8f',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-12-28 10:27:00',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nmobc5v4k62n',
                'quantity' => 123,
                'post_fee' => 0,
                'item_id' => 588595,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '3nmobc5v4k62n',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 58545,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateTest',
                ],
            ],
            [
                'created_time' => '2016-12-28 10:31:39',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3eqgq1gdgqzq7',
                'quantity' => 123,
                'post_fee' => 0,
                'item_id' => 588612,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '3eqgq1gdgqzq7',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 58542,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateTest',
                ],
            ],
        ]);
        $result = $sdk->getInventoryItems();
        $this->assertNotEmpty($result);
        $this->assertCount(10, $result);
        $sdk->shouldHaveReceived('post', [
            'youzan.items.inventory.get',
            '3.0.0',
            ['page_size' => 300],
            'response.items'
        ]);
    }

    public function testGetShopBaseInfo()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'sid' => '18898637',
            'name' => 'yz测试zhangtao10',
            'logo' => 'http://img.yzcdn.cn/111',
            'url' => 'https://h5.youzan.com/v2/showcase/homepage?alias=12oxz98n0',
            'physical_url' => '',
            'cert_type' => 0,
        ]);
        $result = $sdk->getShopBaseInfo();
        $this->assertEquals('18898637', $result['sid']);
    }

    /**
     * only test v4.0.0
     */
    public function testGetTrade()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'delivery_order' => [
                [
                    'pk_id' => 2621633,
                    'express_state' => 1,
                    'orders' => [
                        [
                            'item_id' => 1436413407076030200,
                        ],
                        [
                            'item_id' => 1436413407076030200,
                        ],
                        [
                            'item_id' => 1436413407076030200,
                        ],
                        [
                            'item_id' => 1436413407076030200,
                        ],
                    ],
                    'express_type' => 0,
                ],
            ],
            'order_promotion' => [
                'item' => [
                    [
                        'is_present' => false,
                        'promotions' => [
                            [
                                'promotion_type' => 'customerDiscount',
                                'promotion_title' => '会员折扣',
                                'promotion_type_name' => '会员折扣',
                                'promotion_type_id' => 10,
                                'decrease' => '10.56',
                            ],
                        ],
                        'item_id' => 1436413407076030200,
                        'goods_id' => 410405681,
                        'sku_id' => 36203237,
                    ],
                    [
                        'is_present' => false,
                        'promotions' => [
                            [
                                'promotion_type' => 'customerDiscount',
                                'promotion_title' => '会员折扣',
                                'promotion_type_name' => '会员折扣',
                                'promotion_type_id' => 10,
                                'decrease' => '18.96',
                            ],
                        ],
                        'item_id' => 1436413407076030200,
                        'goods_id' => 409382504,
                        'sku_id' => 36218994,
                    ],
                    [
                        'is_present' => false,
                        'promotions' => [
                            [
                                'promotion_type' => 'customerDiscount',
                                'promotion_title' => '会员折扣',
                                'promotion_type_name' => '会员折扣',
                                'promotion_type_id' => 10,
                                'decrease' => '6.96',
                            ],
                        ],
                        'item_id' => 1436413407076030200,
                        'goods_id' => 409545437,
                        'sku_id' => 36196197,
                    ],
                    [
                        'is_present' => false,
                        'promotions' => [
                            [
                                'promotion_type' => 'customerDiscount',
                                'promotion_title' => '会员折扣',
                                'promotion_type_name' => '会员折扣',
                                'promotion_type_id' => 10,
                                'decrease' => '8.28',
                            ],
                        ],
                        'item_id' => 1436413407076030200,
                        'goods_id' => 409558156,
                        'sku_id' => 36199433,
                    ],
                ],
                'order_decrease' => '7.64',
                'goods_decrease' => '44.76',
                'order' => [
                    [
                        'promotion_type' => 'coupon',
                        'promotion_title' => '福当先！领福券！',
                        'promotion_type_name' => '优惠卡券',
                        'promotion_type_id' => 105,
                        'decrease' => '7.64',
                    ],
                ],
            ],
            'refund_order' => [],
            'full_order_info' => [
                'address_info' => [
                    'self_fetch_info' => '',
                    'delivery_address' => '下沙泰然广场707-706',
                    'delivery_postal_code' => '',
                    'receiver_name' => '李维',
                    'delivery_province' => '广东省',
                    'delivery_city' => '深圳市',
                    'delivery_district' => '福田区',
                    'address_extra' => '{"areaCode":"440304","lon":114.03030714899697,"lat":22.53446797278408}',
                    'receiver_tel' => '13760105707',
                ],
                'remark_info' => [
                    'buyer_message' => '',
                    'star' => 0,
                    'trade_memo' => '27413-582七款T集合 彩虹 L-1 到货补发 3.9 刘 补发单号3869691192817 林3.10 27413 暂时未到 3.13（已留言） 刘',
                ],
                'pay_info' => [
                    'outer_transactions' => [
                        '4200000051201803085172488366',
                    ],
                    'post_fee' => '0.00',
                    'total_fee' => '373.00',
                    'payment' => '320.60',
                    'transaction' => [
                        '180308214647000011',
                    ],
                ],
                'buyer_info' => [
                    'buyer_phone' => '13760105707',
                    'fans_type' => 9,
                    'buyer_id' => 493309932,
                    'fans_id' => 2082418008,
                    'fans_nickname' => '',
                ],
                'orders' => [
                    [
                        'outer_sku_id' => '',
                        'buyer_messages' => '',
                        'item_id' => 1436413407076030200,
                        'item_type' => 0,
                        'price' => '88.00',
                        'num' => 1,
                        'total_fee' => '77.44',
                        'goods_id' => 410405681,
                        'sku_id' => 36203237,
                        'sku_properties_name' => '[{"k":"颜色","k_id":1,"v":"黑色","v_id":137},{"k":"尺码","k_id":12,"v":"L","v_id":4}]',
                        'payment' => '75.65',
                        'title' => '28316 彰显气质风度！ 最低调！ 经典！豪华德国机器工艺！夏季刺绣T恤！',
                    ],
                    [
                        'outer_sku_id' => '',
                        'buyer_messages' => '',
                        'item_id' => 1436413407076030200,
                        'item_type' => 0,
                        'price' => '158.00',
                        'num' => 1,
                        'total_fee' => '139.04',
                        'goods_id' => 409382504,
                        'sku_id' => 36218994,
                        'sku_properties_name' => '[{"k":"颜色","k_id":1,"v":"牛仔蓝","v_id":838},{"k":"尺码","k_id":12,"v":"34","v_id":144}]',
                        'payment' => '135.80',
                        'title' => '27328 让你完美自信的出门！走哪都是焦点！精英男士的高功能商务牛仔裤！',
                    ],
                    [
                        'outer_sku_id' => '',
                        'buyer_messages' => '',
                        'item_id' => 1436413407076030200,
                        'item_type' => 0,
                        'price' => '58.00',
                        'num' => 1,
                        'total_fee' => '51.04',
                        'goods_id' => 409545437,
                        'sku_id' => 36196197,
                        'sku_properties_name' => '[{"k":"颜色","k_id":1,"v":"彩虹","v_id":317799},{"k":"尺码","k_id":12,"v":"L","v_id":4}]',
                        'payment' => '49.85',
                        'title' => '27413 高端LAB支线联名！罕见珍迹版本！Old School复古系列短袖，7款合集！',
                    ],
                    [
                        'outer_sku_id' => '',
                        'buyer_messages' => '',
                        'item_id' => 1436413407076030200,
                        'item_type' => 0,
                        'price' => '69.00',
                        'num' => 1,
                        'total_fee' => '60.72',
                        'goods_id' => 409558156,
                        'sku_id' => 36199433,
                        'sku_properties_name' => '[{"k":"颜色","k_id":1,"v":"白色","v_id":187},{"k":"尺码","k_id":12,"v":"XL","v_id":199}]',
                        'payment' => '59.30',
                        'title' => '27414 合作款 海外版 时尚的弄潮儿 必然要来一发 最具代表性的就是普通的短Tee',
                    ],
                ],
                'source_info' => [
                    'is_offline_order' => false,
                    'source' => [
                        'platform' => 'other',
                        'wx_entrance' => 'direct_buy',
                    ],
                ],
                'order_info' => [
                    'consign_time' => '2018-03-09 21:00:08',
                    'order_extra' => [],
                    'created' => '2018-03-08 21:46:44',
                    'status_str' => '已发货',
                    'expired_time' => '2018-03-09 07:46:44',
                    'success_time' => '1970-01-01 08:00:00',
                    'type' => 0,
                    'tid' => 'E20180308214644100400012',
                    'confirm_time' => '',
                    'pay_time' => '2018-03-08 21:46:52',
                    'update_time' => '2018-03-09 21:00:08',
                    'is_retail_order' => false,
                    'pay_type' => 10,
                    'team_type' => 0,
                    'refund_state' => 0,
                    'close_type' => 0,
                    'status' => 'TRADE_CLOSED',
                    'express_type' => 0,
                    'order_tags' => [],
                ],
            ],
        ]);
        $reuslt = $sdk->getTrade('E20180308214644100400012', '4.0.0');
        $this->assertEquals('E20180308214644100400012', $reuslt['full_order_info']['order_info']['tid']);
        $sdk->shouldHaveReceived('post', [
            'youzan.trade.get',
            '4.0.0',
            ['tid' => 'E20180308214644100400012'],
            'response'
        ]);
    }

    public function testGivePresent()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "is_success" => true,
            "present_id" => 7026144,
            "present_name" => "鸡蛋一枚",
            "receive_address" => "https://h5.youzan.com/v2/showcase/goods?alias=35vf98xpexgsm&present=1gkx4yiy1"
        ]);
        $result = $sdk->givePresent(1, 4851134360);
        $this->assertEquals([
            "is_success" => true,
            "present_id" => 7026144,
            "present_name" => "鸡蛋一枚",
            "receive_address" => "https://h5.youzan.com/v2/showcase/goods?alias=35vf98xpexgsm&present=1gkx4yiy1"
        ],$result);
        $sdk->shouldHaveReceived('post', [
            'youzan.ump.present.give',
            '3.0.0',
            [
                'activity_id' => 1,
                'fans_id' => 4851134360
            ]
        ]);
    }

    public function testPointIncrease()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "is_success" => "true"
        ]);
        $result = $sdk->pointIncrease(1,'4851134360');
        $this->assertTrue($result);
        $sdk->shouldHaveReceived('post', [
            'youzan.crm.customer.points.increase',
            '3.0.1',
            [
                'points' => 1,
                'fans_id' => '4851134360'
            ]
        ]);

        $sdk->pointIncrease(1, '15281009123');
        $sdk->shouldHaveReceived('post', [
            'youzan.crm.customer.points.increase',
            '3.0.1',
            [
                'points' => 1,
                'mobile' => '15281009123'
            ]
        ]);

        $sdk->pointIncrease(1, '15281009123', true);
        $sdk->shouldHaveReceived('post', [
            'youzan.crm.customer.points.increase',
            '3.0.1',
            [
                'points' => 1,
                'open_user_id' => '15281009123'
            ]
        ]);
    }

    public function testGetPresents()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            [
                'end_at' => '2018-10-31 10:21:36',
                'fetch_limit' => 0,
                'item' => [
                    'promotion_cid' => 0,
                    'item_type' => 0,
                    'buy_quota' => '0',
                    'num' => 10,
                    'delivery_template_fee' => null,
                    'template_title' => '',
                    'has_component' => false,
                    'item_weight' => 0,
                    'price' => '1.00',
                    'post_type' => null,
                    'delivery_template_name' => '',
                    'order' => 0,
                    'tag_ids' => '',
                    'is_supplier_item' => false,
                    'item_tags' => [],
                    'virtual_type' => 0,
                    'created' => '2018-10-23 10:17:46',
                    'item_validity_start' => 0,
                    'is_listing' => true,
                    'is_used' => false,
                    'outer_buy_url' => '',
                    'stock_locked' => 0,
                    'effective_type' => 0,
                    'share_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3np7fiu2ko76t&from=wsc&kdtfrom=wsc',
                    'pic_thumb_url' => 'https://img.yzcdn.cn/upload_files/2018/10/18/FlAMOufhu-spQqzfE2qrKfe-tjNU.jpeg!120x120.jpg',
                    'delivery_template_id' => 0,
                    'is_lock' => false,
                    'messages' => [],
                    'origin_price' => '',
                    'pic_url' => 'https://img.yzcdn.cn/upload_files/2018/10/18/FlAMOufhu-spQqzfE2qrKfe-tjNU.jpeg',
                    'cid' => 0,
                    'desc' => '',
                    'is_virtual' => false,
                    'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3np7fiu2ko76t&from=wsc&kdtfrom=wsc',
                    'auto_listing_time' => '0',
                    'skus' => [],
                    'post_fee' => '0.00',
                    'delivery_template_valuation_type' => 1,
                    'num_iid' => '440012111',
                    'title' => '测试赠品',
                    'join_level_discount' => '1',
                    'outer_id' => '',
                    'kdt_id' => '63077',
                    'holidays_available' => 0,
                    'alias' => '3np7fiu2ko76t',
                    'item_validity_end' => 0,
                    'item_imgs' => [
                        [
                            'thumbnail' => 'https://img.yzcdn.cn/upload_files/2018/10/18/FlAMOufhu-spQqzfE2qrKfe-tjNU.jpeg?imageView2/2/w/290/h/290/q/75/format/jpeg',
                            'created' => '2018-10-25 09:57:15',
                            'medium' => 'https://img.yzcdn.cn/upload_files/2018/10/18/FlAMOufhu-spQqzfE2qrKfe-tjNU.jpeg?imageView2/2/w/600/h/0/q/75/format/jpeg',
                            'id' => 1206991786,
                            'url' => 'https://img.yzcdn.cn/upload_files/2018/10/18/FlAMOufhu-spQqzfE2qrKfe-tjNU.jpeg',
                            'combine' => 'https://img.yzcdn.cn/upload_files/2018/10/18/FlAMOufhu-spQqzfE2qrKfe-tjNU.jpeg?imageView2/2/w/600/h/0/q/75/format/jpeg',
                        ],
                    ],
                    'sold_num' => 0,
                    'product_type' => '0',
                    'effective_delay_hours' => 0,
                    'template_id' => 0,
                ],
                'present_id' => 357916,
                'created' => '2018-10-23 10:25:32',
                'title' => '测试',
                'start_at' => '2018-10-23 10:21:36',
            ],
            [
                'end_at' => '2018-10-31 14:06:27',
                'fetch_limit' => 1,
                'item' => [
                    'promotion_cid' => 0,
                    'item_type' => 0,
                    'buy_quota' => '1',
                    'num' => 19,
                    'delivery_template_fee' => null,
                    'template_title' => '',
                    'has_component' => false,
                    'item_weight' => 0,
                    'price' => '0.01',
                    'post_type' => null,
                    'delivery_template_name' => '',
                    'order' => 0,
                    'tag_ids' => '',
                    'is_supplier_item' => false,
                    'item_tags' => [],
                    'virtual_type' => 0,
                    'created' => '2018-10-22 14:06:03',
                    'item_validity_start' => 0,
                    'is_listing' => true,
                    'is_used' => false,
                    'outer_buy_url' => '',
                    'stock_locked' => 0,
                    'effective_type' => 0,
                    'share_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nizt0g6im0hx&from=wsc&kdtfrom=wsc',
                    'pic_thumb_url' => 'https://img.yzcdn.cn/upload_files/2018/10/22/96064343f1724806def68b39e6b8e550.jpg!120x120.jpg',
                    'delivery_template_id' => 0,
                    'is_lock' => false,
                    'messages' => [],
                    'origin_price' => '',
                    'pic_url' => 'https://img.yzcdn.cn/upload_files/2018/10/22/96064343f1724806def68b39e6b8e550.jpg',
                    'cid' => 0,
                    'desc' => '',
                    'is_virtual' => false,
                    'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nizt0g6im0hx&from=wsc&kdtfrom=wsc',
                    'auto_listing_time' => '0',
                    'skus' => [],
                    'post_fee' => '0.01',
                    'delivery_template_valuation_type' => 1,
                    'num_iid' => '439926098',
                    'title' => '赠品11',
                    'join_level_discount' => '1',
                    'outer_id' => '',
                    'kdt_id' => '63077',
                    'holidays_available' => 0,
                    'alias' => '3nizt0g6im0hx',
                    'item_validity_end' => 0,
                    'item_imgs' => [
                        [
                            'thumbnail' => 'https://img.yzcdn.cn/upload_files/2018/10/22/96064343f1724806def68b39e6b8e550.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                            'created' => '2018-10-25 09:57:15',
                            'medium' => 'https://img.yzcdn.cn/upload_files/2018/10/22/96064343f1724806def68b39e6b8e550.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                            'id' => 1210627453,
                            'url' => 'https://img.yzcdn.cn/upload_files/2018/10/22/96064343f1724806def68b39e6b8e550.jpg',
                            'combine' => 'https://img.yzcdn.cn/upload_files/2018/10/22/96064343f1724806def68b39e6b8e550.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        ],
                    ],
                    'sold_num' => 1,
                    'product_type' => '0',
                    'effective_delay_hours' => 0,
                    'template_id' => 0,
                ],
                'present_id' => 357761,
                'created' => '2018-10-22 14:07:00',
                'title' => '草莓花',
                'start_at' => '2018-10-22 14:06:38',
            ],
            [
                'end_at' => '2019-07-31 17:58:01',
                'fetch_limit' => 100,
                'item' => [
                    'promotion_cid' => 0,
                    'item_type' => 0,
                    'buy_quota' => '0',
                    'num' => 9999997,
                    'delivery_template_fee' => null,
                    'template_title' => '',
                    'has_component' => false,
                    'item_weight' => 0,
                    'price' => '0.02',
                    'post_type' => null,
                    'delivery_template_name' => '',
                    'order' => 0,
                    'tag_ids' => '',
                    'is_supplier_item' => false,
                    'item_tags' => [],
                    'virtual_type' => 0,
                    'created' => '2018-10-23 17:59:13',
                    'item_validity_start' => 0,
                    'is_listing' => true,
                    'is_used' => false,
                    'outer_buy_url' => '',
                    'stock_locked' => 0,
                    'effective_type' => 0,
                    'share_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2xcoml65sbw05&from=wsc&kdtfrom=wsc',
                    'pic_thumb_url' => 'https://img.yzcdn.cn/upload_files/2018/10/23/Fj308n3C7ycfGfv3V-4I_mHphhpl.png!120x120.jpg',
                    'delivery_template_id' => 0,
                    'is_lock' => false,
                    'messages' => [],
                    'origin_price' => '',
                    'pic_url' => 'https://img.yzcdn.cn/upload_files/2018/10/23/Fj308n3C7ycfGfv3V-4I_mHphhpl.png',
                    'cid' => 0,
                    'desc' => '',
                    'is_virtual' => false,
                    'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2xcoml65sbw05&from=wsc&kdtfrom=wsc',
                    'auto_listing_time' => '0',
                    'skus' => [],
                    'post_fee' => '0.00',
                    'delivery_template_valuation_type' => 1,
                    'num_iid' => '440032215',
                    'title' => '鸡—赠品',
                    'join_level_discount' => '1',
                    'outer_id' => '',
                    'kdt_id' => '63077',
                    'holidays_available' => 0,
                    'alias' => '2xcoml65sbw05',
                    'item_validity_end' => 0,
                    'item_imgs' => [
                        [
                            'thumbnail' => 'https://img.yzcdn.cn/upload_files/2018/10/23/Fj308n3C7ycfGfv3V-4I_mHphhpl.png?imageView2/2/w/290/h/290/q/75/format/png',
                            'created' => '2018-10-25 09:57:15',
                            'medium' => 'https://img.yzcdn.cn/upload_files/2018/10/23/Fj308n3C7ycfGfv3V-4I_mHphhpl.png?imageView2/2/w/600/h/0/q/75/format/png',
                            'id' => 1211728675,
                            'url' => 'https://img.yzcdn.cn/upload_files/2018/10/23/Fj308n3C7ycfGfv3V-4I_mHphhpl.png',
                            'combine' => 'https://img.yzcdn.cn/upload_files/2018/10/23/Fj308n3C7ycfGfv3V-4I_mHphhpl.png?imageView2/2/w/600/h/0/q/75/format/png',
                        ],
                    ],
                    'sold_num' => 2,
                    'product_type' => '0',
                    'effective_delay_hours' => 0,
                    'template_id' => 0,
                ],
                'present_id' => 358095,
                'created' => '2018-10-23 17:59:30',
                'title' => '鸡—赠品',
                'start_at' => '2018-10-23 17:58:09',
            ],
        ]);
        $result = $sdk->getPresents();
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    public function testGetUnfinishedCoupons()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            [
                'is_at_least' => 0,
                'end_at' => '2018-01-13 10:26:31',
                'is_sync_weixin' => 0,
                'is_random' => 0,
                'is_forbid_preference' => 0,
                'description' => '',
                'title' => '砸金蛋',
                'start_at' => '2018-01-11 10:26:32',
                'total' => 100,
                'expire_notice' => 0,
                'quota' => 0,
                'stock' => 100,
                'value' => '10.00',
                'stat_fetch_num' => 0,
                'value_random_to' => '0.00',
                'is_share' => 1,
                'stat_use_num' => 0,
                'stat_fetch_user_num' => 0,
                'created' => '2018-01-11 10:26:38',
                'range_type' => 'ALL',
                'fetch_url' => 'https://h5.youzan.com/v2/ump/promocard/fetch?alias=1bdq4xpil',
                'user_level_name' => '',
                'group_id' => '2261432',
                'weixin_card_id' => '',
                'updated' => '',
                'coupon_type' => 'PROMOCARD',
                'need_user_level' => 0,
                'at_least' => '0.00',
                'status' => 0,
            ],
            [
                'is_at_least' => 0,
                'end_at' => '2018-04-30 00:00:00',
                'is_sync_weixin' => 0,
                'is_random' => 0,
                'is_forbid_preference' => 0,
                'description' => '',
                'title' => '优惠码',
                'start_at' => '2017-04-28 00:00:00',
                'total' => 200,
                'expire_notice' => 0,
                'quota' => 5,
                'stock' => 199,
                'value' => '200.00',
                'stat_fetch_num' => 1,
                'value_random_to' => '0.00',
                'is_share' => 1,
                'stat_use_num' => 0,
                'stat_fetch_user_num' => 1,
                'created' => '2018-01-09 21:18:48',
                'range_type' => 'ALL',
                'fetch_url' => 'https://h5.youzan.com/v2/showcase/promocode/fetch?alias=whqed57j',
                'user_level_name' => '',
                'group_id' => '2257754',
                'weixin_card_id' => '',
                'updated' => '',
                'coupon_type' => 'PROMOCODE',
                'need_user_level' => 0,
                'at_least' => '0.00',
                'status' => 0,
            ],
            [
                'is_at_least' => 0,
                'end_at' => '2018-01-12 17:32:08',
                'is_sync_weixin' => 0,
                'is_random' => 0,
                'is_forbid_preference' => 0,
                'description' => '',
                'title' => '测试优惠码',
                'start_at' => '2018-01-08 17:32:13',
                'total' => 100,
                'expire_notice' => 0,
                'quota' => 5,
                'stock' => 97,
                'value' => '1.00',
                'stat_fetch_num' => 3,
                'value_random_to' => '0.00',
                'is_share' => 0,
                'stat_use_num' => 3,
                'stat_fetch_user_num' => 1,
                'created' => '2018-01-08 17:32:22',
                'range_type' => 'ALL',
                'fetch_url' => 'https://h5.youzan.com/v2/showcase/promocode/fetch?alias=14uirg1k4',
                'user_level_name' => '',
                'group_id' => '2254619',
                'weixin_card_id' => '',
                'updated' => '',
                'coupon_type' => 'PROMOCODE',
                'need_user_level' => 0,
                'at_least' => '0.00',
                'status' => 0,
            ],
        ]);
        $result = $sdk->getUnfinishedCoupons();
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    public function testGetCoupon()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'date_type' => '1',
            'valid_start_time' => '2017-12-07 16:13:36',
            'is_sync_weixin' => '0',
            'preferential_type' => '1',
            'is_forbid_preference' => '0',
            'denominations' => '1',
            'discount' => '0',
            'description' => '',
            'created_at' => '2017-12-07 16:13:48',
            'group_type' => '7',
            'title' => '7FM券-1',
            'kdt_id' => '16719442',
            'expire_notice' => '0',
            'updated_at' => NULL,
            'total_take' => '5',
            'stock_qty' => '17',
            'valid_end_time' => '2017-12-09 16:13:22',
            'id' => '2182618',
            'fixed_term' => '0',
            'is_invalid' => '0',
            'is_limit' => '0',
            'value_random_to' => '0',
            'is_share' => '1',
            'range_value' => '[{"goods_type":1,"goods_id":"400249027"}]',
            'total_fans_taked' => '5',
            'total_qty' => '22',
            'range_type' => 'part',
            'total_used' => '0',
            'condition' => '2',
            'user_level' => '0',
            'fixed_begin_term' => '0',
        ]);
        $result = $sdk->getCoupon('2182618');
        $this->assertEquals('2182618', $result['id']);
    }

    public function testTakeCoupon()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'coupon_type' => 'PROMOCARD',
            'promocard' => [
                'promocard_id' => '10422654',
                'title' => '华宇测试0722',
                'value' => '1.00',
                'condition' => '无限制',
                'start_at' => '2016-07-22 17:35:03',
                'end_at' => '2016-07-30 17:34:23',
                'is_used' => '0',
                'is_invalid' => '0',
                'is_expired' => 0,
                'background_color' => '#55bd47',
                'detail_url' => 'https://wap.koudaitong.com/v2/showcase/coupon/detail?alias=1359928&id=10422654',
                'verify_code' => '792873936041',
            ],
        ]);
        $result = $sdk->takeCoupon([
            'fans_id' => '4851134360',
            'coupon_group_id' => 1
        ]);
        $this->assertEquals('10422654', $result['promocard']['promocard_id']);
    }

    public function testGetCouponList()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            [
                'date_type' => '1',
                'valid_start_time' => '2018-01-11 10:26:32',
                'is_sync_weixin' => '0',
                'preferential_type' => '1',
                'is_forbid_preference' => '0',
                'denominations' => '1000',
                'discount' => '0',
                'description' => '',
                'created_at' => '2018-01-11 10:26:38',
                'group_type' => '7',
                'title' => '砸金蛋',
                'kdt_id' => '19415975',
                'expire_notice' => '0',
                'updated_at' => null,
                'total_take' => '0',
                'stock_qty' => '100',
                'valid_end_time' => '2018-01-13 10:26:31',
                'id' => '2261432',
                'fixed_term' => '0',
                'is_invalid' => '0',
                'is_limit' => '0',
                'value_random_to' => '0',
                'is_share' => '1',
                'range_value' => '',
                'total_fans_taked' => '0',
                'total_qty' => '100',
                'range_type' => 'all',
                'total_used' => '0',
                'condition' => '0',
                'user_level' => '0',
                'fixed_begin_term' => '0',
            ],
            [
                'date_type' => '2',
                'valid_start_time' => '2018-01-09 16:03:39',
                'is_sync_weixin' => '0',
                'preferential_type' => '2',
                'is_forbid_preference' => '0',
                'denominations' => '0',
                'discount' => '11',
                'description' => '',
                'created_at' => '2018-01-09 16:03:39',
                'group_type' => '7',
                'title' => '测试优惠券01',
                'kdt_id' => '19415975',
                'expire_notice' => '0',
                'updated_at' => null,
                'total_take' => '0',
                'stock_qty' => '100',
                'valid_end_time' => '9999-01-01 00:00:00',
                'id' => '2256905',
                'fixed_term' => '1',
                'is_invalid' => '0',
                'is_limit' => '4',
                'value_random_to' => '0',
                'is_share' => '1',
                'range_value' => '',
                'total_fans_taked' => '0',
                'total_qty' => '100',
                'range_type' => 'all',
                'total_used' => '0',
                'condition' => '0',
                'user_level' => '100120053',
                'fixed_begin_term' => '0',
            ],
            [
                'date_type' => '2',
                'valid_start_time' => '2018-01-09 14:58:20',
                'is_sync_weixin' => '0',
                'preferential_type' => '1',
                'is_forbid_preference' => '0',
                'denominations' => '300',
                'discount' => '0',
                'description' => '',
                'created_at' => '2018-01-09 14:58:20',
                'group_type' => '7',
                'title' => '会员等级',
                'kdt_id' => '19415975',
                'expire_notice' => '0',
                'updated_at' => null,
                'total_take' => '0',
                'stock_qty' => '100',
                'valid_end_time' => '9999-01-01 00:00:00',
                'id' => '2256512',
                'fixed_term' => '2',
                'is_invalid' => '0',
                'is_limit' => '0',
                'value_random_to' => '0',
                'is_share' => '1',
                'range_value' => '',
                'total_fans_taked' => '0',
                'total_qty' => '100',
                'range_type' => 'all',
                'total_used' => '0',
                'condition' => '0',
                'user_level' => '100120053',
                'fixed_begin_term' => '1',
            ],
            [
                'date_type' => '2',
                'valid_start_time' => '2018-01-09 11:09:20',
                'is_sync_weixin' => '0',
                'preferential_type' => '1',
                'is_forbid_preference' => '0',
                'denominations' => '1000',
                'discount' => '0',
                'description' => '',
                'created_at' => '2018-01-09 11:09:20',
                'group_type' => '7',
                'title' => '动态有效期次日2',
                'kdt_id' => '19415975',
                'expire_notice' => '0',
                'updated_at' => null,
                'total_take' => '3',
                'stock_qty' => '97',
                'valid_end_time' => '9999-01-01 00:00:00',
                'id' => '2255807',
                'fixed_term' => '3',
                'is_invalid' => '0',
                'is_limit' => '0',
                'value_random_to' => '0',
                'is_share' => '1',
                'range_value' => '',
                'total_fans_taked' => '3',
                'total_qty' => '100',
                'range_type' => 'all',
                'total_used' => '0',
                'condition' => '0',
                'user_level' => '0',
                'fixed_begin_term' => '1',
            ],
        ]);
        $result = $sdk->getCouponList();
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result);
    }

    public function testGetSalesman()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'seller' => '3dcYpJ',
            'from_buyer_mobile' => '',
            'money' => '61.51',
            'mobile' => '15281009123',
            'nickname' => 'z🤡',
            'created_at' => '2018-12-01 17:04:18',
            'order_num' => 41,
            'fans_id' => 4851134360,
        ]);
        $result = $sdk->getSalesman([
            'mobile' => '15281009123',
            'fans_type' => '1',
            'fans_id' => '0'
        ]);
        $this->assertEquals('15281009123', $result['mobile']);
        $sdk->shouldHaveReceived('post', [
            'youzan.salesman.account.get',
            '3.0.0',
            [
                'mobile' => '15281009123',
                'fans_type' => '1',
                'fans_id' => '0'
            ]
        ]);
    }

    public function testGetSalesmanList()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            'accounts' => [
                [
                    'seller' => 'o2CfxJ',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '1',
                    'nickname' => '－',
                    'created_at' => '2018-10-29 17:07:49',
                    'order_num' => 0,
                    'fans_id' => 0,
                ],
                [
                    'seller' => '419zwJ',
                    'from_buyer_mobile' => '',
                    'money' => '0.03',
                    'mobile' => '13003615259',
                    'nickname' => 'ciel',
                    'created_at' => '2018-10-29 16:02:36',
                    'order_num' => 1,
                    'fans_id' => 6223990772,
                ],
                [
                    'seller' => '4lpp5E',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '18358187290',
                    'nickname' => '－',
                    'created_at' => '2018-10-25 17:55:31',
                    'order_num' => 0,
                    'fans_id' => 0,
                ],
                [
                    'seller' => '3zSCJh',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '15829681283',
                    'nickname' => '王浩',
                    'created_at' => '2018-10-25 17:55:29',
                    'order_num' => 0,
                    'fans_id' => 5996075709,
                ],
                [
                    'seller' => '3T2NMa',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '13917210397',
                    'nickname' => '靖靖',
                    'created_at' => '2018-10-17 10:50:12',
                    'order_num' => 0,
                    'fans_id' => 6081912422,
                ],
                [
                    'seller' => 'ooTD2w',
                    'from_buyer_mobile' => '',
                    'money' => '0.02',
                    'mobile' => '13588254457',
                    'nickname' => 'Mervyn',
                    'created_at' => '2018-10-17 10:50:12',
                    'order_num' => 1,
                    'fans_id' => 5858637392,
                ],
                [
                    'seller' => 'ogI4Zc',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '18512137110',
                    'nickname' => 'Jacq-宫元',
                    'created_at' => '2018-10-17 10:50:11',
                    'order_num' => 0,
                    'fans_id' => 0,
                ],
                [
                    'seller' => '319f5C',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '18858197472',
                    'nickname' => '余晖',
                    'created_at' => '2018-10-17 10:50:02',
                    'order_num' => 0,
                    'fans_id' => 6102121917,
                ],
                [
                    'seller' => 'oobZn3',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '15757184650',
                    'nickname' => '达达',
                    'created_at' => '2018-10-17 10:50:02',
                    'order_num' => 0,
                    'fans_id' => 5926377028,
                ],
                [
                    'seller' => 'o3nPKZ',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '15011077963',
                    'nickname' => '邸佳松',
                    'created_at' => '2018-10-17 10:50:02',
                    'order_num' => 0,
                    'fans_id' => 6192422439,
                ],
                [
                    'seller' => 'oddaHS',
                    'from_buyer_mobile' => '',
                    'money' => '0.02',
                    'mobile' => '15221885452',
                    'nickname' => 'John',
                    'created_at' => '2018-10-17 10:50:02',
                    'order_num' => 4,
                    'fans_id' => 6239635916,
                ],
                [
                    'seller' => '418yFd',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '15381065080',
                    'nickname' => '－',
                    'created_at' => '2018-10-17 10:50:02',
                    'order_num' => 0,
                    'fans_id' => 6266694360,
                ],
                [
                    'seller' => '2xU1za',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '13732254016',
                    'nickname' => '夕陽下•美了Jay影',
                    'created_at' => '2018-10-17 10:50:01',
                    'order_num' => 0,
                    'fans_id' => 6298081685,
                ],
                [
                    'seller' => '3yXXQS',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '13819467377',
                    'nickname' => '张益群',
                    'created_at' => '2018-10-17 10:50:01',
                    'order_num' => 0,
                    'fans_id' => 6421669983,
                ],
                [
                    'seller' => 'ocZSdV',
                    'from_buyer_mobile' => '',
                    'money' => '0.00',
                    'mobile' => '13738097284',
                    'nickname' => '荣',
                    'created_at' => '2018-10-17 10:50:01',
                    'order_num' => 0,
                    'fans_id' => 6437146906,
                ],
            ],
            'total_results' => 64,
        ]);
        $result = $sdk->getSalesmanList();
        $this->assertCount(15, $result['accounts']);
        $this->assertEquals(64, $result['total_results']);
    }

    public function testTradeRefundAgree()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "is_success" => true
        ]);
        $result = $sdk->tradeRefundAgree('1234567890', '0987654321');
        $this->assertTrue($result);
        $sdk->shouldHaveReceived('post',[
            'youzan.trade.refund.agree',
            '3.0.0',
            [
                'refund_id' => '1234567890',
                'version' => '0987654321'
            ]
        ]);
    }

    public function testTradeRefundRefuse()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "is_success" => true
        ]);
        $result = $sdk->tradeRefundRefuse('1234567890', 'refuse_mark' ,'0987654321');
        $this->assertTrue($result);
        $sdk->shouldHaveReceived('post', [
            'youzan.trade.refund.refuse',
            '3.0.0',
            [
                'refund_id' => '1234567890',
                'remark' => 'refuse_mark',
                'version' => '0987654321'
            ]
        ]);
    }

    public function testTicketCreate()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "boolean" => true
        ]);
        $result = $sdk->ticketCreate('test-ticket', '1234567890');
        $this->assertTrue($result);
        $sdk->shouldHaveReceived('post', [
            'youzan.ebiz.external.ticket.create',
            '1.0.0',
            [
                'tickets' => 'test-ticket',
                'orderNo' => '1234567890',
                'singleNum' => 1
            ],
        ]);
    }

    public function testTicketVerify()
    {
        $sdk = $this->mockSdk(['post']);
        $sdk->shouldReceive('post')->andReturn([
            "boolean" => true
        ]);
        $result = $sdk->ticketVerify([
            'tickets' => 'test-ticket',
            'orderNo' => '1234567890',
            'verifySerial' => '0987654321',
            'num' => 1,
            'historyNum' => 1,
            'singleLeftNum' => 0
        ]);
        $this->assertTrue($result);
    }

    public function testGetProducts()
    {
        $sdk = $this->mockSdk(['getInventoryItems', 'getOnSaleItems']);
        $sdk->shouldReceive('getInventoryItems')->andReturn([
            [
                'created_time' => '2016-06-29 10:24:37',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3f0alqhw1qx8v',
                'quantity' => 1999987,
                'post_fee' => 1,
                'item_id' => 45401,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 228,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/13/Fk3a06DYP6H1hpinttIWlmdM7DUH.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '分销猫',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 2,
                'alias' => '3f0alqhw1qx8v',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 3232,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '山猫测试',
                ],
            ],
            [
                'created_time' => '2016-08-28 15:53:20',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nrnf9nfkf5wf',
                'quantity' => 109,
                'post_fee' => 0,
                'item_id' => 219758,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 278,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/22/FsAOkfHtPXSXKeNMNX9yT9Ok2ctw.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'suguangrong测试4',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 300,
                'alias' => '3nrnf9nfkf5wf',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-08-28 15:53:41',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=36dwwa287g6fj',
                'quantity' => 999960,
                'post_fee' => 0,
                'item_id' => 219783,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 9,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/03/25/FvZyAy20s36rGLIWzgsp3essG22U.JPG?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'ly新分销商品',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 20000,
                'alias' => '36dwwa287g6fj',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-11-18 17:10:33',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y3y20svfv667',
                'post_fee' => 0,
                'item_id' => 460396,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2029,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '商品啊',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '1y3y20svfv667',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-11-21 16:29:14',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3f5971z5xt6kv',
                'post_fee' => 0,
                'item_id' => 465155,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '3f5971z5xt6kv',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-11-18 17:10:28',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2g1dqvegvoar3',
                'post_fee' => 0,
                'item_id' => 460394,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2029,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/19/Fk8CoaU1Pf7d4QQa0AjZ1_OCSxsb.png?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '商品啊111',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '2g1dqvegvoar3',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '0.0',
                    'delivery_template_id' => 2,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => '浙江省包邮',
                ],
            ],
            [
                'created_time' => '2016-12-28 10:21:01',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2fss76ht4cklb',
                'quantity' => 123,
                'post_fee' => 0,
                'item_id' => 588593,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '2fss76ht4cklb',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 58621,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateTest',
                ],
            ],
            [
                'created_time' => '2016-12-28 10:50:14',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y3yxdefikm8f',
                'quantity' => 200,
                'post_fee' => 0,
                'item_id' => 588614,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest有规格分销商品',
                'item_no' => 'ADFA',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 99,
                'alias' => '1y3yxdefikm8f',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-12-28 10:27:00',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nmobc5v4k62n',
                'quantity' => 123,
                'post_fee' => 0,
                'item_id' => 588595,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '3nmobc5v4k62n',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 58545,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateTest',
                ],
            ],
            [
                'created_time' => '2016-12-28 10:31:39',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3eqgq1gdgqzq7',
                'quantity' => 123,
                'post_fee' => 0,
                'item_id' => 588612,
                'item_type' => 10,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:57:31',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 2305,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/09/22/FkTxFeZIlPk78F04Ugaxwy0nmdwS.jpeg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'sgrtest',
                'item_no' => '',
                'update_time' => '2017-02-21 14:34:07',
                'price' => 90,
                'alias' => '3eqgq1gdgqzq7',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 58542,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateTest',
                ],
            ],
        ]);
        $sdk->shouldReceive('getOnSaleItems')->andReturn([
            [
                'created_time' => '2016-07-18 15:18:00',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=27bbv813gfcwf',
                'quantity' => 1111,
                'post_fee' => 0,
                'item_id' => 87823,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290977,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/da2466f4e47f58ce030c84b3a1bdc99d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290980,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/8dc87296f76454e5421e9fb02cca3c41.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290984,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/93ce89d9207d638c39e3719c42c0b15e.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290993,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/ebea7b7977f149422215dfd4372477ab.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 603290996,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/18/9302d6a983a02b97b7df2539e6a68b58.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'Haier/海尔BCD-160TMPQ冰箱小型双门式 家用双开门 两门节能冰箱',
                'item_no' => '',
                'update_time' => '2016-07-18 15:18:33',
                'price' => 105900,
                'alias' => '27bbv813gfcwf',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 29,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateZJ',
                ],
            ],
            [
                'created_time' => '2016-07-26 14:02:02',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=272of885gb0lr',
                'quantity' => 99,
                'post_fee' => 1,
                'item_id' => 104270,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal2016072614020224397',
                'item_no' => '',
                'update_time' => '2016-07-26 14:02:02',
                'price' => 100,
                'alias' => '272of885gb0lr',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-07-26 14:02:25',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1y7mke8ktca33',
                'quantity' => 99,
                'post_fee' => 1,
                'item_id' => 104272,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal2016072614022545260',
                'item_no' => '',
                'update_time' => '2016-07-26 14:02:25',
                'price' => 100,
                'alias' => '1y7mke8ktca33',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-07-21 16:44:26',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2odyltuod88tr',
                'quantity' => 1905,
                'post_fee' => 0,
                'item_id' => 95211,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033917,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/b16c0fe22de196144cfe5b32bd4bf8b1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033920,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/1078b68a8d4796486fcc0a46c3e30884.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033923,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/9f63596e3de39210ce7818cd941d679a.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033926,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/32ae50a4e777b64ef46bbdc70b66b165.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 605033933,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/07/21/bd35e7369d231153b7bc35dacb3278cb.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => '【丑羽】狂神耐打羽毛球  12只装耐打王羽毛球 一个顶两ymq羽毛球',
                'item_no' => '',
                'update_time' => '2016-09-12 11:04:46',
                'price' => 1100,
                'alias' => '2odyltuod88tr',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 11:55:37',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=3nlghuiefl3bj',
                'quantity' => 10000,
                'post_fee' => 0,
                'item_id' => 345,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291155361',
                'item_no' => '',
                'update_time' => '2016-04-29 11:55:37',
                'price' => 1,
                'alias' => '3nlghuiefl3bj',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 11:57:04',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=366hm5dkf8xm7',
                'quantity' => 10000,
                'post_fee' => 0,
                'item_id' => 347,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291157030',
                'item_no' => '',
                'update_time' => '2016-04-29 11:57:04',
                'price' => 1,
                'alias' => '366hm5dkf8xm7',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 12:02:04',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2g3wc88z6h30f',
                'quantity' => 100,
                'post_fee' => 0,
                'item_id' => 352,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291202045',
                'item_no' => '',
                'update_time' => '2017-01-09 14:25:35',
                'price' => 1,
                'alias' => '2g3wc88z6h30f',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 32,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateZJ',
                ],
            ],
            [
                'created_time' => '2016-04-29 11:57:29',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=272pz6ig1nxr3',
                'quantity' => 10000,
                'post_fee' => 0,
                'item_id' => 349,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291157284',
                'item_no' => '',
                'update_time' => '2016-04-29 11:57:29',
                'price' => 1,
                'alias' => '272pz6ig1nxr3',
                'post_type' => 1,
                'delivery_template' => [],
            ],
            [
                'created_time' => '2016-04-29 11:57:29',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=1ydujozuyzh3z',
                'quantity' => 1000,
                'post_fee' => 0,
                'item_id' => 350,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [],
                'title' => 'Normal201604291157297',
                'item_no' => '',
                'update_time' => '2016-04-29 11:57:29',
                'price' => 1,
                'alias' => '1ydujozuyzh3z',
                'post_type' => 2,
                'delivery_template' => [
                    'delivery_template_fee' => '.00',
                    'delivery_template_id' => 31,
                    'delivery_template_valuation_type' => 1,
                    'delivery_template_name' => 'templateZJ',
                ],
            ],
            [
                'created_time' => '2016-06-01 17:55:55',
                'detail_url' => 'https://h5.youzan.com/v2/showcase/goods?alias=2x5blpxeqf5fz',
                'quantity' => 234,
                'post_fee' => 0,
                'item_id' => 15657,
                'item_type' => 0,
                'num' => 0,
                'item_imgs' => [
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503943,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/91498b357109fb7adacaba086e4fc3f1.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503945,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/c28c6bab0c8dc99094d3d00c54530611.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503948,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/185933d7c9fbe47e22dcd0c88bbd073d.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503951,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/1579dd69bf253f9597ce68808ae9f16f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                    [
                        'thumbnail' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg?imageView2/2/w/290/h/290/q/75/format/jpg',
                        'created' => '2017-09-15 10:58:34',
                        'medium' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                        'id' => 577503954,
                        'url' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg',
                        'combine' => 'https://img.yzcdn.cn/upload_files/2016/06/01/71203d8c899b2bd9a8f2ec6163d77d9f.jpg?imageView2/2/w/600/h/0/q/75/format/jpg',
                    ],
                ],
                'title' => 'suguangrong普通商品3',
                'item_no' => '',
                'update_time' => '2016-06-30 19:49:13',
                'price' => 1200,
                'alias' => '2x5blpxeqf5fz',
                'post_type' => 1,
                'delivery_template' => [],
            ],
        ]);
        $result = $sdk->getProducts();
        $this->assertNotEmpty($result);
        $this->assertCount(20, $result);
        $sdk->shouldHaveReceived('getInventoryItems', [
            ['page_size' => 300],
            '3.0.0'
        ]);
        $sdk->shouldHaveReceived('getOnSaleItems', [
            ['page_size' => 300],
            '3.0.0'
        ]);
    }
}