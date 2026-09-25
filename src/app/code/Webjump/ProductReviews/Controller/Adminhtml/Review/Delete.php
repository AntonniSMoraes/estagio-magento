<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Psr\Log\LoggerInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Webjump_ProductReviews::reviews';
    private ReviewRepositoryInterface $repository;
    private DataPersistorInterface $persistor;
    private LoggerInterface $logger;

    public function __construct(Context $context, ReviewRepositoryInterface $repository, DataPersistorInterface $persistor, LoggerInterface $logger)
    {
        parent::__construct($context);
        $this->repository = $repository;
        $this->persistor = $persistor;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $id = (int) $this->getRequest()->getParam('entity_id');
            $this->repository->deleteById($id);
            $this->persistor->clear('webjump_productreviews_review');
            $this->messageManager->addSuccessMessage(__('Avaliação excluída com sucesso.'));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->messageManager->addErrorMessage(__('Não foi possível excluir a avaliação. Atualize a listagem e tente novamente.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
