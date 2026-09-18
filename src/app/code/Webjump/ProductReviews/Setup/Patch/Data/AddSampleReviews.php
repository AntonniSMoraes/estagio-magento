<?php
declare(strict_types=1);

namespace Webjump\ProductReviews\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Webjump\ProductReviews\Api\ReviewRepositoryInterface;
use Webjump\ProductReviews\Model\ReviewFactory;

class AddSampleReviews implements DataPatchInterface
{
    private ReviewRepositoryInterface $reviewRepository;
    private ReviewFactory $reviewFactory;

    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        ReviewFactory $reviewFactory
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->reviewFactory = $reviewFactory;
    }

    public function apply(): self
    {
        $samples = [
            [
                'customer_name' => 'Carlos Silva',
                'product_sku' => 'cam-est-26',
                'rating' => 5,
                'comment' => 'Camiseta excelente, tecido muito confortável e sustentável!',
                'is_approved' => true
            ],
            [
                'customer_name' => 'Mariana Souza',
                'product_sku' => 'cam-est-26',
                'rating' => 4,
                'comment' => 'Ótimo caimento, porém a entrega atrasou um dia.',
                'is_approved' => true
            ],
            [
                'customer_name' => 'João Pereira',
                'product_sku' => 'cam-est-26',
                'rating' => 2,
                'comment' => 'Não gostei da cor, esperava algo diferente.',
                'is_approved' => false
            ],
            [
                'customer_name' => 'Beatriz Lima',
                'product_sku' => 'cam-est-26',
                'rating' => 5,
                'comment' => 'Qualidade impecável, recomendo a todos!',
                'is_approved' => true
            ],
            [
                'customer_name' => 'Lucas Rocha',
                'product_sku' => 'cam-est-26',
                'rating' => 3,
                'comment' => 'O tamanho ficou um pouco justo.',
                'is_approved' => false
            ]
        ];

        foreach ($samples as $data) {
            $review = $this->reviewFactory->create();
            $review->setCustomerName($data['customer_name']);
            $review->setProductSku($data['product_sku']);
            $review->setRating($data['rating']);
            $review->setComment($data['comment']);
            $review->setIsApproved($data['is_approved']);
            $this->reviewRepository->save($review);
        }

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}