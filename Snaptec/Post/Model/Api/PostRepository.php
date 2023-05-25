<?php

namespace Snaptec\Post\Model\Api;

use Snaptec\Post\Api\PostRepositoryInterface;
use Snaptec\Post\Model\ImageUploader;
use Snaptec\Post\Model\PostFactory;
class PostRepository implements PostRepositoryInterface {

    protected $postFactory;
    protected $imageUploader;
    public function __construct
    (
        PostFactory $postFactory,
        ImageUploader $imageUploader
    )
    {
        $this->postFactory = $postFactory;
        $this->imageUploader = $imageUploader;
    }

    public function create($id = null, $title, $content)
    {
        if (empty($title)) {
            throw new \InvalidArgumentException("Title cannot be empty.");
        }
        if (empty($content)) {
            throw new \InvalidArgumentException("Content cannot be empty.");
        }
        $post = $this->postFactory->create();
        switch($id){
            case $id == null:
                break;
            case !is_numeric($id):
                throw new \InvalidArgumentException(__("Post required is number", $id));
                break;
            case $id !== null:
            $post = $post->load($id);
            if (!$post->getId()) {
                throw new \InvalidArgumentException(__("Post with ID %1 does not exist.", $id));
            }
            break;

        }
        $targetPath = 'post/images';
        $fileImage = $this->imageUploader->uploadImage($post->getImage(),$targetPath);
        try {
            $post->setTitle($title);
            $post->setContent($content);
            $post->setImage($fileImage);
            $post->save();
        }catch(NoSuchEntityException $e){
            return $e;
        }
        $result = [];
       return $result = ['id' => $post->getId(),
        'title'=> $post->getTitle(),
        'content'=> $post->getContent(),
        'image'=> $post->getImage(),
        ];
    }
}


?>