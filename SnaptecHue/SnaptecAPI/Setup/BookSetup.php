<?php

namespace SnaptecHue\SnaptecAPI\Setup;

use Magento\Eav\Setup\EavSetup;

class BookSetup extends EavSetup
{

    public function getDefaultEntities()
    {
        /* create our own entity */
        $employeeEntity = \SnaptecHue\SnaptecAPI\Model\Book::ENTITY;

        $entities = [
            $employeeEntity => [
                'entity_model' => 'SnaptecHue\SnaptecAPI\Model\ResourceModel\Book',
                'table' => $employeeEntity . '_entity',
                'attributes' => [
                    'title' => [
                        'type' => 'static'
                    ],
                    'content' => [
                        'type' => 'static'
                    ],
                    'image' => [
                        'type' => 'static'
                    ],
                    'created_at'=>[
                        'type' => 'static'
                    ],
                    'updated_at'=>[
                        'type' => 'static'
                    ]
                ],
            ],
        ];
        return $entities;
    }
}
