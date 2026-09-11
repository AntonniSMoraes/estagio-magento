<?php
declare(strict_types=1);

namespace Webjump\PromoBanner\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Banner implements ArgumentInterface
{
    private const XML_PATH_ENABLED     = 'promo_banner/general/enabled';
    private const XML_PATH_TITLE       = 'promo_banner/general/title';
    private const XML_PATH_DESCRIPTION = 'promo_banner/general/description';
    private const XML_PATH_COUPON      = 'promo_banner/general/coupon';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTitulo(): string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE
        );

        return $value ? (string) $value : 'Oferta Especial';
    }

    public function getDescricao(): string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );

        return $value ? (string) $value : 'Confira os produtos com desconto!';
    }

    public function getCupom(): string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_COUPON,
            ScopeInterface::SCOPE_STORE
        );

        return $value ? (string) $value : '';
    }
}