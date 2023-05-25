<?php

namespace SnaptecHue\SnaptecAPI\Model\ResourceModel\Book;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'SnaptecHue\SnaptecAPI\Model\Book',
            'SnaptecHue\SnaptecAPI\Model\ResourceModel\Book'
        );
    }
}
