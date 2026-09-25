<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Block\Product;

use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Store\Model\ScopeInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;

class Reviews extends AbstractView implements IdentityInterface
{
    private ReviewRepositoryInterface $repository;
    private SearchCriteriaBuilder $criteriaBuilder;
    private SortOrderBuilder $sortOrderBuilder;
    private ?array $reviews = null;

    public function __construct(Context $context, ArrayUtils $arrayUtils, ReviewRepositoryInterface $repository, SearchCriteriaBuilder $criteriaBuilder, SortOrderBuilder $sortOrderBuilder, array $data = [])
    {
        parent::__construct($context, $arrayUtils, $data);
        $this->repository = $repository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    public function getReviews(): array
    {
        if ($this->reviews !== null) { return $this->reviews; }
        $this->reviews = [];
        if (!$this->_scopeConfig->isSetFlag('webjump_productreviews/general/enabled', ScopeInterface::SCOPE_STORE) || !$this->getProduct()) {
            return $this->reviews;
        }
        $minimum = max(1, min(5, (int) $this->_scopeConfig->getValue('webjump_productreviews/general/minimum_rating', ScopeInterface::SCOPE_STORE)));
        $sort = $this->sortOrderBuilder->setField('created_at')->setDirection('DESC')->create();
        $criteria = $this->criteriaBuilder
            ->addFilter('product_sku', $this->getProduct()->getSku())
            ->addFilter('is_approved', 1)
            ->addFilter('rating', $minimum, 'gteq')
            ->addSortOrder($sort)->setPageSize(10)->setCurrentPage(1)->create();
        $this->reviews = $this->repository->getList($criteria)->getItems();
        return $this->reviews;
    }

    public function getIdentities()
    {
        return [\Webjump\ProductReviews\Model\Review::CACHE_TAG];
    }
}
