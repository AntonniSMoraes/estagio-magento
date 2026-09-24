<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Block\Adminhtml\Review\Edit;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    private Context $context;
    public function __construct(Context $context) { $this->context = $context; }
    public function getButtonData()
    {
        return ['label' => __('Voltar'), 'class' => 'back', 'sort_order' => 10,
            'on_click' => 'location.href = ' . json_encode($this->context->getUrlBuilder()->getUrl('*/*/index'))];
    }
}
