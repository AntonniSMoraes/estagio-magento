<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;
use Webjump\ProductReviews\Model\ReviewFactory;
use Webjump\ProductReviews\Model\ReviewValidator;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Webjump_ProductReviews::reviews';
    private ReviewRepositoryInterface $repository;
    private ReviewFactory $reviewFactory;
    private ReviewValidator $validator;
    private ProductRepositoryInterface $products;
    private DataPersistorInterface $persistor;
    private LoggerInterface $logger;

    public function __construct(
        Context $context,
        ReviewRepositoryInterface $repository,
        ReviewFactory $reviewFactory,
        ReviewValidator $validator,
        ProductRepositoryInterface $products,
        DataPersistorInterface $persistor,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->reviewFactory = $reviewFactory;
        $this->validator = $validator;
        $this->products = $products;
        $this->persistor = $persistor;
        $this->logger = $logger;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $redirect = $this->resultRedirectFactory->create();
        if (!is_array($data) || !$data) {
            return $redirect->setPath('*/*/index');
        }
        $rawId = $data['entity_id'] ?? '';
        if (!is_scalar($rawId) || ((string) $rawId !== '' && !ctype_digit((string) $rawId))) {
            $this->messageManager->addErrorMessage(__('ID de avaliação inválido.'));
            return $redirect->setPath('*/*/index');
        }
        $id = (int) $rawId;
        try {
            $clean = $this->validator->validate($data);
            $this->products->get($clean['product_sku']);
            $review = $id ? $this->repository->getById($id) : $this->reviewFactory->create();
            $review->setCustomerName($clean['customer_name']);
            $review->setProductSku($clean['product_sku']);
            $review->setComment($clean['comment']);
            $review->setRating($clean['rating']);
            $review->setIsApproved((bool) $clean['is_approved']);
            $this->repository->save($review);
            $this->persistor->clear('webjump_productreviews_review');
            $this->messageManager->addSuccessMessage(__('Avaliação salva com sucesso.'));
            return $redirect->setPath('*/*/index');
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->messageManager->addErrorMessage(__('Não foi possível salvar a avaliação. Consulte os logs.'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(__('Ocorreu um erro ao salvar a avaliação.'));
        }
        $safe = array_intersect_key($data, array_flip(['customer_name', 'product_sku', 'comment', 'rating', 'is_approved']));
        $safe = array_filter($safe, 'is_scalar');
        $safe['entity_id'] = $id ?: '';
        $this->persistor->set('webjump_productreviews_review', $safe);
        return $redirect->setPath($id ? '*/*/edit' : '*/*/new', $id ? ['entity_id' => $id] : []);
    }
}
