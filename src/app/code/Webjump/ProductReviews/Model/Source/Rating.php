<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Model\Source;
use Magento\Framework\Data\OptionSourceInterface;
class Rating implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        for ($i = 1; $i <= 5; $i++) { $options[] = ['value' => $i, 'label' => (string) $i]; }
        return $options;
    }
}
