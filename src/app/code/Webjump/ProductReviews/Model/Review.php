<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Model;

use Magento\Framework\Model\AbstractModel;
use Webjump\ProductReviews\Api\Data\ReviewInterface;

class Review extends AbstractModel implements ReviewInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Review::class);
    }

    public function getCustomerName(): ?string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    public function setCustomerName(string $name): ReviewInterface
    {
        return $this->setData(self::CUSTOMER_NAME, $name);
    }

    public function getProductSku(): ?string
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    public function setProductSku(string $sku): ReviewInterface
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    public function getRating(): ?int
    {
        return (int) $this->getData(self::RATING);
    }

    public function setRating(int $rating): ReviewInterface
    {
        return $this->setData(self::RATING, $rating);
    }

    public function getComment(): ?string
    {
        return $this->getData(self::COMMENT);
    }

    public function setComment(string $comment): ReviewInterface
    {
        return $this->setData(self::COMMENT, $comment);
    }

    public function isApproved(): bool
    {
        return (bool) $this->getData(self::IS_APPROVED);
    }

    public function setIsApproved(bool $isApproved): ReviewInterface
    {
        return $this->setData(self::IS_APPROVED, $isApproved);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }
}