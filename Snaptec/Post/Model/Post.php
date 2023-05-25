<?php

namespace Snaptec\Post\Model;

class Post extends \Magento\Framework\Model\AbstractModel {

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    protected function _construct() {
        $this->_init('Snaptec\Post\Model\ResourceModel\Post');
    }

}
