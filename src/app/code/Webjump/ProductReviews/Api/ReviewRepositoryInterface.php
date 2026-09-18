<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Webjump\ProductReviews\Api\Data\ReviewInterface;

interface ReviewRepositoryInterface
{
    public function save(ReviewInterface $review): ReviewInterface;
    public function getById(int $id): ReviewInterface;
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
    public function delete(ReviewInterface $review): bool;
    public function deleteById(int $id): bool;
}