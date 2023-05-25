<?php

namespace Snaptec\Post\Model\Resolver;

use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Snaptec\Post\Model\PostFactory;
use Snaptec\Post\Helper\Data as HelperData;
use Snaptec\Post\Model\ResourceModel\Post\Collection;


class Post implements ResolverInterface
{

    protected $_postFactory;
    protected $_helper;
    protected $_collection;



    public function __construct(
        PostFactory $postFactory,
        HelperData $helper,
        Collection $collection

    ) {
        $this->_postFactory = $postFactory->create();
        $this->_helper = $helper;
        $this->_collection = $collection;
    }

    /**
     * Handle save post in graphql
     * @return void
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {

        $title = $args['input']['title'] ?? '';
        $content = $args['input']['content'] ?? '';
        $image = $args['input']['image'] ?? '';
        $postId = $args['input']['postId'] ?? '';

        // Validate request
        $this->_helper->checkEmtyDataOfField(['title' => $title, 'content' => $content, 'image' => $image]);

        try {

            // Handle data if post exits
            if (!empty($postId)) {
                $post = $this->_postFactory->load($postId);
                if (!$post->getId()) {
                    throw new Exception(__("The post doesn't exist"));
                }

                // Check the post exits
                if ($this->_helper->checkPostAlreadyExits($title)) {
                    throw new Exception(__("The title already exist"));
                }

                $urlImage = $this->_helper->uploadImageBase64($image);

                //  Delete old image in dir of the post
                $this->_helper->deleteOldImage($post->getImage());

                $data = [
                    'title' => $title,
                    'content' => $content,
                    'image' => $urlImage
                ];
                $this->_postFactory
                    ->setEntityId($postId)
                    ->setTitle($title)
                    ->setContent($content)
                    ->setImage($urlImage)
                    ->save();

                return $data;
            }

            // Check the post exits
            if ($this->_helper->checkPostAlreadyExits($title)) {
                throw new Exception(__("The title already exist"));
            }

            $imageUrl = $this->_helper->uploadImageBase64($image);

            $data = [
                'title' => $title,
                'content' => $content,
                'image' => $imageUrl
            ];
            $this->_postFactory
                ->setTitle($title)
                ->setContent($content)
                ->setImage($imageUrl)
                ->save();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
