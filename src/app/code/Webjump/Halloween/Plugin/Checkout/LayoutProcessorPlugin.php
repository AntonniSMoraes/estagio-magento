<?php
declare(strict_types=1);

namespace Webjump\Halloween\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

class LayoutProcessorPlugin
{
    public function afterProcess(LayoutProcessor $subject, array $result): array
    {
        $shipping = &$result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children'];

        $shipping['before-shipping-method-form']['children']['mensagem_bruxaria'] = [
            'component' => 'Magento_Ui/js/form/element/textarea',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/textarea'
            ],
            'dataScope' => 'shippingAddress.mensagem_bruxaria',
            'label' => __('Haunted message on the package'),
            'notice' => __('Optional. Maximum 200 characters.'),
            'provider' => 'checkoutProvider',
            'sortOrder' => 250,
            'validation' => ['max_text_length' => 200],
            'required' => false,
            'value' => ''
        ];

        return $result;
    }
}
