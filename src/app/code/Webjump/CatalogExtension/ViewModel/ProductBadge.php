<?php
declare(strict_types=1);

namespace Webjump\CatalogExtension\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductBadge implements ArgumentInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getCurrentProduct(): ?Product
    {
        $product = $this->registry->registry('current_product');
        return ($product instanceof Product) ? $product : null;
    }

    public function isSustainable(): bool
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return false;
        }

        return (bool) $product->getData('is_sustainable');
    }
}