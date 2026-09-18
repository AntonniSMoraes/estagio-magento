<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Api\Data;

interface ReviewInterface
{
    public const ENTITY_ID = 'entity_id';
    public const CUSTOMER_NAME = 'customer_name';
    public const PRODUCT_SKU = 'product_sku';
    public const RATING = 'rating';
    public const COMMENT = 'comment';
    public const IS_APPROVED = 'is_approved';
    public const CREATED_AT = 'created_at';

    public function getId();
    public function getCustomerName(): ?string;
    public function setCustomerName(string $name): self;
    public function getProductSku(): ?string;
    public function setProductSku(string $sku): self;
    public function getRating(): ?int;
    public function setRating(int $rating): self;
    public function getComment(): ?string;
    public function setComment(string $comment): self;
    public function isApproved(): bool;
    public function setIsApproved(bool $isApproved): self;
    public function getCreatedAt(): ?string;
}