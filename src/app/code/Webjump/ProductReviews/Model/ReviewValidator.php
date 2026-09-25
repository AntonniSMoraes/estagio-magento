<?php
declare(strict_types=1);
namespace Webjump\ProductReviews\Model;

use Magento\Framework\Exception\LocalizedException;

class ReviewValidator
{
    public function validate(array $data): array
    {
        $clean = [];
        foreach (['customer_name' => 255, 'product_sku' => 64, 'comment' => 65535] as $field => $limit) {
            if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
                throw new LocalizedException(__('Preencha autor, SKU e comentário.'));
            }
            $clean[$field] = trim($data[$field]);
            if (mb_strlen($clean[$field]) > $limit || ($field === 'comment' && strlen($clean[$field]) > $limit)) {
                throw new LocalizedException(__('O campo %1 ultrapassa o tamanho permitido.', $field));
            }
        }
        $rating = $data['rating'] ?? null;
        if (!is_scalar($rating) || !in_array((string) $rating, ['1', '2', '3', '4', '5'], true)) {
            throw new LocalizedException(__('A nota deve ser um número inteiro entre 1 e 5.'));
        }
        $approved = $data['is_approved'] ?? '0';
        if (!is_scalar($approved) || !in_array((string) $approved, ['0', '1'], true)) {
            throw new LocalizedException(__('Selecione um status de aprovação válido.'));
        }
        $clean['rating'] = (int) $rating;
        $clean['is_approved'] = (int) $approved;
        return $clean;
    }
}
