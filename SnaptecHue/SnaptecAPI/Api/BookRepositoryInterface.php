<?php

namespace SnaptecHue\SnaptecAPI\Api;
use SnaptecHue\SnaptecAPI\Api\Data\BookInterface;
interface BookRepositoryInterface{

    /**
     * Create product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     */
    // public function save(\SnaptecHue\SnaptecAPI\Api\Data\BookInterface $book);

    /**
     * Save book.
     *
     * @param \SnaptecHue\SnaptecAPI\Api\Data\BookInterface $book
     * @return \SnaptecHue\SnaptecAPI\Api\Data\BookInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BookInterface $book);

    /**
     * Get by id
     *
     * @param int $bookId
     *
     * @return \SnaptecHue\SnaptecAPI\Api\Data\BookInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($bookId);

    /**
     * Save book.
     *
     * @param \SnaptecHue\SnaptecAPI\Api\Data\BookInterface $book
     * @return \SnaptecHue\SnaptecAPI\Api\Data\BookInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveGraphQL(BookInterface $book);
} 