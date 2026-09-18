<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Model\ResourceModel\Review;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Webjump\ProductReviews\Model\Review as ModelReview;
use Webjump\ProductReviews\Model\ResourceModel\Review as ResourceReview;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct(): void
    {
        $this->_init(ModelReview::class, ResourceReview::class);
    }
}