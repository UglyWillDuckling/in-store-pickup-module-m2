<?php

namespace GaussDev\InStore\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->createLocationTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create location table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function createLocationTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
                       ->newTable($setup->getTable('gaussdev_store_locations'))
                       ->addColumn(
                           'location_id',
                           Table::TYPE_INTEGER,
                           null,
                           ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                           'ID'
                       )
                       ->addColumn(
                           'name',
                           Table::TYPE_TEXT,
                           255,
                           ['nullable' => false],
                           'Name'
                       )
                        ->addColumn(
                            'street',
                            Table::TYPE_TEXT,
                            255,
                            ['nullable' => false],
                            'Street'
                        )
                        ->addColumn(
                            'city',
                            Table::TYPE_TEXT,
                            255,
                            ['nullable' => false],
                            'City'
                        )
                       ->addColumn(
                            'postcode',
                            Table::TYPE_TEXT,
                            10,
                            ['nullable' => true],
                            'PostCode'
                        );
        $setup->getConnection()->createTable($table);

        return $this;
    }
}
