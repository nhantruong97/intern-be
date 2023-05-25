<?php

namespace SnaptecHue\SnaptecAPI\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;

class Book extends AbstractEntity
{
    protected function _construct() {
        $this->_read = 'snaptechue_snaptecapi_book_read';
        $this->_write = 'snaptechue_snaptecapi_book_write';
    }
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\SnaptecHue\SnaptecAPI\Model\Book::ENTITY);
        }
        return parent::getEntityType();
    }
}
