<?php

namespace SnaptecHue\SnaptecAPI\Api\Data;

interface BookInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const TITLE = 'title';

    const CONTENT = 'content';

    const IMAGE = 'image';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const ATTRIBUTES = [
        self::TITLE,
        self::CONTENT,
        self::IMAGE,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
    /**#@-*/

    /**
     * Book id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set book id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * book title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set book title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * book content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Set book content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \SnaptecHue\SnaptecAPI\Api\Data\BookInterface
     */
    public function setImage($image);
    /**
     * book created date
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set book created date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * book updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set book updated date
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}
