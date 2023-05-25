<?php
namespace Snaptec\Post\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'lctiendat_api_post_collection';
    protected $_eventObject = 'post_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Snaptec\Post\Model\Post', 'Snaptec\Post\Model\ResourceModel\Post');
    }
}
