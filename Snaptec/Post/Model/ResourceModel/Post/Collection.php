<?php

namespace Snaptec\Post\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(){
        $this->_init('Snaptec\Post\Model\Post', 'Snaptec\Post\Model\ResourceModel\Post');
    }
}
