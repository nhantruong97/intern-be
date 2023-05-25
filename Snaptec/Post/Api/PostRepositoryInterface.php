<?php
namespace Snaptec\Post\Api;

interface PostRepositoryInterface
{
    /**
     * @param string $id
     * @param string $title
     * @param string $content
     * @return array 
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function create($id = null, $title,$content);
}
