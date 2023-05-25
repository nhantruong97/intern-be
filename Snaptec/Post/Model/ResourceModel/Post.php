<?php

namespace Snaptec\Post\Model\ResourceModel;


class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct()
    {
        // Table name and primary column
        $this->_init('post','id');
    }

    // protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    // {
    //     $oldImage = $object->getOrigData('image');
    //     $newImage = $object->getData('image');

    //     if($newImage != null && $newImage != $oldImage){
    //         $imageUploader = \Magento\Framework\App\ObjectManager::getInstance()
    //         ->get('Snaptec\Brand\BrandImageUpload');
    //         $imageUploader->moveFileFromTmp($newImage);
    //     }
    //     return $this;
    // }
}



