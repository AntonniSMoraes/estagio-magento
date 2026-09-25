<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Ui\Component;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ExportButton extends \Magento\Ui\Component\ExportButton
{
    private AuthorizationInterface $authorization;

    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $urlBuilder, $components, $data);
        $this->authorization = $authorization;
    }

    public function prepare()
    {
        parent::prepare();
        if (!$this->authorization->isAllowed('Webjump_ProductReviews::export')) {
            $config = $this->getData('config');
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
