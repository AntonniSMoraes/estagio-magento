<?php
declare(strict_types=1);

namespace Webjump\CatalogExtension\Plugin;

use Magento\Catalog\Model\Product;

class ProductPlugin
{
    public function afterGetName(Product $subject, ?string $result): ?string
    {
        if (empty($result)) {
            return $result;
        }

        return $result . ' - [Exclusivo Webjump]';
    }
}