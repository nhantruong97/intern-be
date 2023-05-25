<?php

namespace Snaptec\Post\Api;

interface PostRepositoryInterface
{
    /**
     * @param string $postId
     * @param string $title,
     * @param string $content
     * @param string $image
     * @return void
     */
    public function save($postId = null, $title, $content, $image = null);
}
