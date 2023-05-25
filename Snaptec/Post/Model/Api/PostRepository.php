<?php

namespace Snaptec\Post\Model\Api;

use Exception;
use Snaptec\Post\Api\PostRepositoryInterface;
use Snaptec\Post\Model\PostFactory;
use Snaptec\Post\Helper\Data as HelperData;
use Psr\Log\LoggerInterface;

class PostRepository implements PostRepositoryInterface
{

    protected $_postFactory;
    protected $_helper;
    protected $_jsonFactory;
    protected $_logger;

    public function __construct(
        PostFactory $postFactory,
        HelperData $helper,
        LoggerInterface $logger
    ) {
        $this->_postFactory = $postFactory->create();
        $this->_helper = $helper;
        $this->_logger = $logger;
    }


    /**
     * Save post
     * @return void
     */
    public function save($postId = null, $title, $content, $image = null)
    {
        try {

            // Validate request 
            $this->_helper->checkEmtyDataOfField(['title' => $title, 'content' => $content]);

            // Hadle data if post exits
            if (!empty($postId)) {
                $post = $this->_postFactory->load($postId);
                if (!$post->getId()) {
                    throw new Exception(__("The post doesn't exist"), 404);
                }

                // Check title is duplicate
                if ($this->_helper->checkPostAlreadyExits($title)) {
                    throw new Exception(__("The title already exist"), 409);
                }
                $urlImage = $this->_helper->uploadImage();

                //  Delete old image in dir of post
                $this->_helper->deleteOldImage($post->getImage());

                $data = [
                    'title' => $title,
                    'content' => $content,
                    'image' => $urlImage,
                    'entity_id' => $postId
                ];

                $this->_postFactory->setData($data)->save();
                unset($data['entity_id']);

                echo json_encode([
                    'success' => true,
                    'data' => $data,
                    'code' => 200
                ]);
            }

            // Check title is duplicate
            if ($this->_helper->checkPostAlreadyExits($title)) {
                throw new Exception(__("The title already exist"), 409);
            }

            $urlImage = $this->_helper->uploadImage();

            $data = [
                'title' => $title,
                'content' => $content,
                'image' => $urlImage
            ];
            $this->_postFactory->setData($data)->save();

            echo json_encode([
                'success' => true,
                'data' => $data,
                'code' => 203
            ]);
        } catch (\Throwable $th) {
            $this->_logger->error($th->getMessage());
            // throw new Exception($th->getMessage());
            echo json_encode([
                'success' => false,
                'data' => $th->getMessage(),
                'code' => $th->getCode()
            ]);
        }
    }
}
