<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Webjump_ProductReviews::reviews';
    private PageFactory $pageFactory;
    private ReviewRepositoryInterface $repository;

    public function __construct(Context $context, PageFactory $pageFactory, ReviewRepositoryInterface $repository)
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->repository = $repository;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                $this->repository->getById($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('A avaliação não existe mais.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/index');
            }
        }
        $page = $this->pageFactory->create();
        $page->setActiveMenu(self::ADMIN_RESOURCE);
        $page->getConfig()->getTitle()->prepend($id ? __('Editar avaliação #%1', $id) : __('Nova avaliação'));
        return $page;
    }
}
