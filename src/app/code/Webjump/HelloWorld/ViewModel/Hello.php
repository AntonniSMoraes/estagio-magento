<?php
namespace Webjump\HelloWorld\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Hello implements ArgumentInterface
{
    public function getMensagem(): string
    {
        return 'Olá, Magento! Meu primeiro módulo está funcionando.';
    }
}