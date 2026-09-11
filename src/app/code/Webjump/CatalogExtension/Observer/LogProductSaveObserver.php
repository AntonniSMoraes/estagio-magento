<?php
declare(strict_types=1);

namespace Webjump\CatalogExtension\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class LogProductSaveObserver implements ObserverInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Catalog\Model\Product|null $product */
        $product = $observer->getEvent()->getData('product');

        if ($product) {
            $this->logger->info(sprintf(
                '[Webjump Observer] Produto salvo com sucesso: SKU=%s, ID=%s, Nome=%s',
                $product->getSku(),
                $product->getId(),
                $product->getName()
            ));
        }
    }
}