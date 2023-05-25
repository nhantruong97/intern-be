<?php

namespace SnaptecHue\SnaptecAPI\Model;

use Magento\Framework\Model\AbstractModel;
use SnaptecHue\SnaptecAPI\Api\Data\BookInterface;

class Book extends AbstractModel implements BookInterface
{
    const ENTITY = 'snaptechue_snaptecapi_book';
    public function _construct()
    {
        $this->_init('SnaptecHue\SnaptecAPI\Model\ResourceModel\Book');
    }

    /**
     * Identifier getter
     *
     * @return int
     */
    public function getId()
    {
        return $this->_getData('entity_id');
    }

    /**
     * Set entity Id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setData('entity_id', $value);
    }

    /**
     * Set book name
     *
     * @param string $name
     * @return $this
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    public function setTitle($title)
    {
        $this->setData(self::TITLE, $title);
    }

    public function getContent()
    {
        return $this->_getData(self::CONTENT);
    }
    public function setContent($content)
    {
        $this->setData(self::CONTENT, $content);
    }

    public function getImage()
    {
        return $this->_getData(self::IMAGE);
    }
    public function setImage($image)
    {
        $this->setData(self::IMAGE, $image);
    }
    /**
     * Set book created date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set book updated date
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
    /**
     * Get book creation date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Get previous book update date
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }
}
