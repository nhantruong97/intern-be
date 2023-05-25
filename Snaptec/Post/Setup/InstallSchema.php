<?php

namespace Snaptec\Post\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Psr\Log\LoggerInterface;

class InstallSchema implements InstallSchemaInterface
{
    private $_logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        try {
            if (!$installer->tableExists('lctiendat_posts')) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('lctiendat_posts'))
                    ->addColumn(
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Post Id'
                    )
                    ->addColumn(
                        'title',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['nullable' => false],
                        'Post Title'
                    )
                    ->addColumn(
                        'content',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['nullable' => false],
                        'Post Content'
                    )
                    ->addColumn(
                        'image',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['nullable' => false],
                        'Post Image'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )->addColumn(
                        'updated_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At'
                    )
                    ->setComment('Table Post');
                $installer->getConnection()->createTable($table);
                $installer->endSetup();
            }
        } catch (\Throwable $th) {
            return $this->_logger->error($th->getMessage());
        }
    }
}
