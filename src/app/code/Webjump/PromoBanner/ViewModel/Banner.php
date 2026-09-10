<?php
declare(strict_types=1);

namespace Webjump\PromoBanner\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Banner implements ArgumentInterface
{
    public function getTitulo(): string
    {
        return 'Semana Especial Webjump';
    }

    public function getDescricao(): string
    {
        return 'Aproveite descontos exclusivos e frete grátis em toda a loja!';
    }

    public function getCupom(): string
    {
        return 'BEMVINDO10';
    }
}