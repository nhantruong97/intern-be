<?php
namespace Snaptec\Banner\Api;

interface BannerInterface
{
    /**
     * Undocumented function
     *
     * @param string $id
     * @param string $title
     * @param string $content
     * @param string|null $image
     * @return int
     */
    public function getBanner($id=null, $title, $content);

}