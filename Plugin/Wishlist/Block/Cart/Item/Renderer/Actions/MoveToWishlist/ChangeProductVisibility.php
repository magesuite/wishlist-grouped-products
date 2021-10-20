<?php

namespace MageSuite\WishlistGroupedProducts\Plugin\Wishlist\Block\Cart\Item\Renderer\Actions\MoveToWishlist;

class ChangeProductVisibility
{
    public function afterIsProductVisibleInSiteVisibility(\Magento\Wishlist\Block\Cart\Item\Renderer\Actions\MoveToWishlist $subject, $result)
    {
        if ($result) {
            return $result;
        }

        $buyRequest = $subject->getItem()->getBuyRequest();
        $superProductConfig = $buyRequest->getData('super_product_config');
        if (isset($superProductConfig['product_type']) && $superProductConfig['product_type'] == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $result = true;
        }

        return $result;
    }
}
