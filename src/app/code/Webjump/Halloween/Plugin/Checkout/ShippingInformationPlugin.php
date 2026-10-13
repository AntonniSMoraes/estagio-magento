<?php
declare(strict_types=1);

namespace Webjump\Halloween\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Webjump\Halloween\Model\MessageValidator;

class ShippingInformationPlugin
{
    private CartRepositoryInterface $quoteRepository;
    private MessageValidator $validator;

    public function __construct(CartRepositoryInterface $quoteRepository, MessageValidator $validator)
    {
        $this->quoteRepository = $quoteRepository;
        $this->validator = $validator;
    }

    public function aroundSaveAddressInformation(
        ShippingInformationManagement $subject,
        callable $proceed,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $extension = $address ? $address->getExtensionAttributes() : null;
        $message = $this->validator->validate($extension ? $extension->getMensagemBruxaria() : null);
        $result = $proceed($cartId, $addressInformation);
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setData('mensagem_bruxaria', $message);
        $this->quoteRepository->save($quote);

        return $result;
    }
}
