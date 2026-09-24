<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Model\Review;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;
use Webjump\ProductReviews\Model\ResourceModel\Review\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private RequestInterface $request;
    private ReviewRepositoryInterface $repository;
    private DataPersistorInterface $persistor;

    public function __construct(
        $name, $primaryFieldName, $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        ReviewRepositoryInterface $repository,
        DataPersistorInterface $persistor,
        array $meta = [], array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->repository = $repository;
        $this->persistor = $persistor;
    }

    public function getData()
    {
        $id = (int) $this->request->getParam('entity_id');
        $data = ['entity_id' => '', 'rating' => '5', 'is_approved' => '0'];
        if ($id) {
            $review = $this->repository->getById($id);
            $data = [
                'entity_id' => $review->getId(),
                'customer_name' => $review->getCustomerName(),
                'product_sku' => $review->getProductSku(),
                'comment' => $review->getComment(),
                'rating' => (string) $review->getRating(),
                'is_approved' => $review->isApproved() ? '1' : '0'
            ];
        }
        $persisted = $this->persistor->get('webjump_productreviews_review');
        if (is_array($persisted) && (int) ($persisted['entity_id'] ?? 0) === $id) {
            $data = array_replace($data, $persisted);
            $this->persistor->clear('webjump_productreviews_review');
        }
        return [$id ?: '' => $data];
    }
}
