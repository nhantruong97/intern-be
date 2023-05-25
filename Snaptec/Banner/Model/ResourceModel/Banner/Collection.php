<?php
namespace Snaptec\Banner\Model\ResourceModel\Banner;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'banner_id';
	protected $_eventPrefix = 'snaptec_banner_banner_collection';
	protected $_eventObject = 'banner_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Snaptec\Banner\Model\Banner', 'Snaptec\Banner\Model\ResourceModel\Banner');
    }
}
