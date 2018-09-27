<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-9-26
 * Time: 下午4:42
 */

namespace Dezsidog\YzSdk\Test;


use Dezsidog\YzSdk\YzOpenSdk;

class SdkTest extends \Orchestra\Testbench\TestCase
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
        $this->sdk = $this->app->make(YzOpenSdk::class);
    }

    protected function getPackageProviders($app)
    {
        return ['Dezsidog\YzSdk\YzSdkServiceProvider'];
    }

    /**
     * @throws \Exception
     */
    public function testImageUpload()
    {
        $param = [
            [
                'url' => __DIR__ . '/img/product.jpg',
                'field' => 'image[]',
            ]
        ];
        $result = $this->sdk->imageUpload($param);
        $this->assertEquals($this->kdt_id, $result['kdt_id']);
        $this->assertNotEmpty($result['image_url']);
        $this->assertNotEmpty($result['image_id']);
    }

    /**
     * @throws \Exception
     */
    public function testItemCreateUpdateDeleteGetUpDown()
    {
        $param = [
            [
                'url' => __DIR__ . '/img/product.jpg',
                'field' => 'image[]',
            ]
        ];
        $result = $this->sdk->imageUpload($param);
        $testProduct = [
            'desc' => 'test product',
            'image_ids' => $result['image_id'],
            'price' => 1,
            'title' => 'test product'
        ];
        $result = $this->sdk->itemCreate($testProduct);
        $this->assertEquals($result['desc'], 'test product');
        $this->assertEquals($result['title'], 'test product');

        $item_id = $result['item_id'];

        $testProduct['desc'] = 'test product2';
        $testProduct['title'] = 'test product2';
        $testProduct['item_id'] = $item_id;
        $result = $this->sdk->itemUpdate($testProduct);
        $this->assertTrue($result);

        $result = $this->sdk->itemGet(['item_id' => $item_id]);
        $this->assertEquals($result['desc'], 'test product2');
        $this->assertEquals($result['title'], 'test product2');

        $result = $this->sdk->itemUpdateListing($item_id);
        $this->assertTrue($result);

        $result = $this->sdk->itemUpdateDelisting($item_id);
        $this->assertTrue($result);

        $result = $this->sdk->itemDelete($item_id);
        $this->assertTrue($result);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateSku()
    {
        $param = [
            [
                'url' => __DIR__ . '/img/product.jpg',
                'field' => 'image[]',
            ]
        ];
        $result = $this->sdk->imageUpload($param);
        $testProduct = [
            'desc' => 'test product',
            'image_ids' => $result['image_id'],
            'price' => 1,
            'title' => 'test product',
            'sku_stocks' => json_encode([
                [
                    'price' => 1,
                    'quantity' => 3,
                    'skus' => [
                        [
                            'k' => '日期',
                            'v' => '2018.9.9'
                        ]
                    ]
                ],
            ]),
        ];
        $result = $this->sdk->itemCreate($testProduct);
        $this->assertEquals(3, $result['skus'][0]['quantity']);
        $item_id = $result['item_id'];
        $sku_id = $result['skus'][0]['sku_id'];

        $result = $this->sdk->skuUpdate([
            'item_id' => $item_id,
            'sku_id' => $sku_id,
            'quantity' => 4
        ]);
        $this->assertTrue($result);

        $result = $this->sdk->itemGet(['item_id' => $item_id]);
        $this->assertEquals(4, $result['skus'][0]['quantity']);

        $this->sdk->itemDelete($item_id);
    }
}