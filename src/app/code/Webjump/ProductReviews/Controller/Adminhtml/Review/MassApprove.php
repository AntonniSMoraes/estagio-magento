<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;
use Webjump\ProductReviews\Model\ResourceModel\Review\CollectionFactory;

class MassApprove extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Webjump_ProductReviews::reviews';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private ReviewRepositoryInterface $reviewRepository;
    private LoggerInterface $logger;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ReviewRepositoryInterface $reviewRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->reviewRepository = $reviewRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        $approved = 0;
        try {
            // Filter also handles "select all" and excluded rows across grid pages.
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            foreach ($collection as $review) {
                if ($review->isApproved()) {
                    continue;
                }
                $review->setIsApproved(true);
                $this->reviewRepository->save($review);
                $approved++;
            }
            $this->messageManager->addSuccessMessage(
                __('%1 avaliação(ões) aprovada(s).', $approved)
            );
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->messageManager->addErrorMessage(
                __('Não foi possível concluir a aprovação. %1 avaliação(ões) já aprovada(s). Verifique a seleção e os logs.', $approved)
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(
                __('Ocorreu um erro ao aprovar as avaliações. %1 avaliação(ões) já aprovada(s). Consulte os logs.', $approved)
            );
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
