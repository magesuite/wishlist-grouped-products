<?php

namespace MageSuite\WishlistGroupedProducts\Test\Integration\Wishlist\Model\Wishlist;

class WishlistTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\TestFramework\Wishlist\Model\GetWishlistByCustomerId
     */
    protected $getWishlistByCustomerId;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $json;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->wishlistFactory = $this->objectManager->get(\Magento\Wishlist\Model\WishlistFactory::class);
        $this->getWishlistByCustomerId = $this->objectManager->get(\Magento\TestFramework\Wishlist\Model\GetWishlistByCustomerId::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->json = $this->objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class);

        $this->productRepository->cleanCache();
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/GroupedProduct/_files/product_grouped_with_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddGroupedProductToWishList(): void
    {
        $simpleProduct = $this->productRepository->get('simple_11');
        $groupedProduct = $this->productRepository->get('grouped');
        $buyRequest = [
            'super_product_config' => [
                'product_type' => 'grouped',
                'product_id' => $groupedProduct->getId()
            ],
            'action' => 'add',
        ];

        $wishlist = $this->getWishlistByCustomerId->execute(1);
        $wishlist->addNewItem($simpleProduct->getId(), $buyRequest);
        $item = $this->getWishlistByCustomerId->getItemBySku(1, 'grouped');

        $this->assertNotNull($item);
        $this->assertEquals('grouped', $item->getProduct()->getSku());

        $buyRequestOption = $item->getOptionByCode('info_buyRequest');
        $this->assertEquals($buyRequest, $this->json->unserialize($buyRequestOption->getValue()));
    }
}
