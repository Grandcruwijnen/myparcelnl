<?php

namespace MyParcelNL\Magento\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory, EavSetupFactory $eavSetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.1.23', '<=')) {

            $setup->startSetup();
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'myparcel_fit_in_mailbox',
                [
                    'type'                    => 'varchar',
                    'backend'                 => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'label'                   => 'Fit in Mailbox',
                    'input'                   => 'select',
                    'class'                   => '',
                    'source'                  => 'MyParcelNL\Magento\Model\Source\FitInMailboxOptions',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => true,
                    'default'                 => null,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => true,
                    'used_in_product_listing' => true,
                    'unique'                  => false,
                    'apply_to'                => 'simple,configurable,bundle,grouped',
                    'group'                   => 'General'
                ]
            );
        }

        /* Set a new 'MyParcel options' group and place the option 'myparcel_fit_in_mailbox' into it */
        if (version_compare($context->getVersion(), '2.5.0', '<=')) {

            $setup->startSetup();
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $groupName = 'MyParcel Options'; /* Label of the group*/
            $entityTypeId = $eavSetup->getEntityTypeId('catalog_product'); /* get entity type id so that attribute are only assigned to catalog_product */
            $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId); /* Here we have fetched all attribute set as we want attribute group to show under all attribute set.*/

            foreach($attributeSetIds as $attributeSetId) {
                $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 19);
                $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

                // Add existing attribute to group
                $attributeId = $eavSetup->getAttributeId($entityTypeId, 'myparcel_fit_in_mailbox');
                $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, null);
            }
        }

        /* Add the option 'Fit in digital stamp' */
        if (version_compare($context->getVersion(), '2.5.0', '<=')) {

            $setup->startSetup();
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'myparcel_digital_stamp',
                [
                    'group' => 'MyParcel Options',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Fit in digital stamp',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '0',
                    'searchable' => true,
                    'filterable' => true,
                    'comparable' => true,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => '',

                ]
            );
        }

        $setup->endSetup();
    }
}