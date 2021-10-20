<?php

namespace MageSuite\WishlistGroupedProducts\Plugin\Wishlist\Model\Wishlist;

class AddGroupedProductFromAssociatedSimple
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    public function __construct(\Magento\Framework\Serialize\Serializer\Json $serializer)
    {
        $this->serializer = $serializer;
    }

    public function beforeAddNewItem(\Magento\Wishlist\Model\Wishlist $subject, $product, $buyRequest = null, $forciblySetQty = false)
    {
        $buyRequest = $this->getBuyRequestDataObject($buyRequest);

        $superProductConfig = $buyRequest->getData('super_product_config');
        if (isset($superProductConfig['product_type']) && $superProductConfig['product_type'] == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $product = $buyRequest->getData('super_product_config')['product_id'];
        }

        return [$product, $buyRequest, $forciblySetQty];
    }

    protected function getBuyRequestDataObject($buyRequest)
    {
        if ($buyRequest instanceof \Magento\Framework\DataObject) {
            return $buyRequest;
        } elseif (is_string($buyRequest)) {
            $isInvalidItemConfiguration = false;
            $buyRequestData = [];
            try {
                $buyRequestData = $this->serializer->unserialize($buyRequest);
                if (!is_array($buyRequestData)) {
                    $isInvalidItemConfiguration = true;
                }
            } catch (\Exception $exception) {
                $isInvalidItemConfiguration = true;
            }
            if ($isInvalidItemConfiguration) {
                throw new \InvalidArgumentException('Invalid wishlist item configuration.');
            }
            return new \Magento\Framework\DataObject($buyRequestData);
        } elseif (is_array($buyRequest)) {
            return new \Magento\Framework\DataObject($buyRequest);
        } else {
            return new \Magento\Framework\DataObject();
        }
    }
}
