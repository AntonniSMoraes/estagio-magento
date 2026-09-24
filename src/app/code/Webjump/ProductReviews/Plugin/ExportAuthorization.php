<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\AuthorizationException;

class ExportAuthorization
{
    private RequestInterface $request;
    private AuthorizationInterface $authorization;
    public function __construct(RequestInterface $request, AuthorizationInterface $authorization)
    {
        $this->request = $request;
        $this->authorization = $authorization;
    }
    public function beforeExecute($subject)
    {
        if ($this->request->getParam('namespace') === 'webjump_productreviews_listing'
            && (!$this->authorization->isAllowed('Webjump_ProductReviews::reviews')
                || !$this->authorization->isAllowed('Webjump_ProductReviews::export'))) {
            throw new AuthorizationException(__('Você não tem permissão para exportar avaliações.'));
        }
        return null;
    }
}
