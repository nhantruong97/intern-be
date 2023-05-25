<?php

namespace SnaptecHue\SnaptecAPI\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    private $bookSetupFactory;

    public function __construct(\SnaptecHue\SnaptecAPI\Setup\BookSetupFactory $bookSetupFactory)
    {
        $this->bookSetupFactory = $bookSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {


        $setup->startSetup();

        $employeeSetup = $this->bookSetupFactory->create(['setup' => $setup]);

        $employeeSetup->installEntities();

        $setup->endSetup();
    }
}
