<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Block\Adminhtml\Review\Edit;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Salvar avaliação'), 'class' => 'save primary', 'sort_order' => 90,
            'data_attribute' => ['mage-init' => ['button' => ['event' => 'save']], 'form-role' => 'save']
        ];
    }
}
