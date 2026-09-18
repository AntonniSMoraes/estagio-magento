<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Webjump\ProductReviews\Api\Data\ReviewInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;
use Webjump\ProductReviews\Model\ResourceModel\Review as ReviewResource;
use Webjump\ProductReviews\Model\ResourceModel\Review\CollectionFactory;

class ReviewRepository implements ReviewRepositoryInterface
{
    private ReviewResource $resource;
    private ReviewFactory $reviewFactory;
    private CollectionFactory $collectionFactory;
    private CollectionProcessorInterface $collectionProcessor;
    private SearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        ReviewResource $resource,
        ReviewFactory $reviewFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->reviewFactory = $reviewFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(ReviewInterface $review): ReviewInterface
    {
        try {
            $this->resource->save($review);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $review;
    }

    public function getById(int $id): ReviewInterface
    {
        $review = $this->reviewFactory->create();
        $this->resource->load($review, $id);
        if (!$review->getId()) {
            throw new NoSuchEntityException(__('Review with id "%1" does not exist.', $id));
        }
        return $review;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(ReviewInterface $review): bool
    {
        try {
            $this->resource->delete($review);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}