<?php
declare(strict_types=1);

namespace Webjump\CatalogExtension\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddSustainableAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private CategorySetupFactory $categorySetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function apply(): self
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeCode = 'is_sustainable';

        $categorySetup->addAttribute(
            Product::ENTITY,
            $attributeCode,
            [
                'type' => 'int',
                'label' => 'Produto Sustentável',
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => true,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'group' => 'Content'
            ]
        );

        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $attributeSetIds = $categorySetup->getAllAttributeSetIds($entityTypeId);

        foreach ($attributeSetIds as $attributeSetId) {
            $groupId = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Content');
            if ($groupId) {
                $categorySetup->addAttributeToSet(
                    $entityTypeId,
                    $attributeSetId,
                    $groupId,
                    $attributeCode
                );
            }
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