<?php
declare(strict_types=1);

namespace Webjump\Halloween\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Webjump\Halloween\Model\MessageValidator;

class CopyMessageToOrder implements ObserverInterface
{
    private MessageValidator $validator;

    public function __construct(MessageValidator $validator)
    {
        $this->validator = $validator;
    }

    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $message = $this->validator->validate($quote->getData('mensagem_bruxaria'));
        $order->setData('mensagem_bruxaria', $message);
    }
}
