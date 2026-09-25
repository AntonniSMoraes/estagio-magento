<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class ReviewActions extends Column
{
    private UrlInterface $urlBuilder;
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, UrlInterface $urlBuilder, array $components = [], array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $params = ['entity_id' => (int) $item['entity_id']];
                $item[$this->getData('name')] = [
                    'edit' => ['href' => $this->urlBuilder->getUrl('webjump_productreviews/review/edit', $params), 'label' => __('Editar')],
                    'delete' => [
                        'href' => $this->urlBuilder->getUrl('webjump_productreviews/review/delete', $params),
                        'label' => __('Excluir'), 'post' => true,
                        'confirm' => ['title' => __('Excluir avaliação'), 'message' => __('Deseja excluir esta avaliação?')]
                    ]
                ];
            }
        }
        return $dataSource;
    }
}
