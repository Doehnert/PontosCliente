<?php

namespace Vexpro\PontosCliente\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface

{
    private $eavSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'pontos_cliente', [
            'type' => 'varchar',
            'label' => 'Pontos Cliente',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 999,
            'system' => 0,
            'default' => '0',
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'saldo_cliente', [
            'type' => 'varchar',
            'label' => 'Saldo Cliente',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 999,
            'system' => 0,
            'default' => '0',
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'pontos_cliente')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_edit', 'customer_account_create'], //you can use other forms also ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
            ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'saldo_cliente')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_edit', 'customer_account_create'], //you can use other forms also ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
            ]);

        $attribute->save();
    }
}
