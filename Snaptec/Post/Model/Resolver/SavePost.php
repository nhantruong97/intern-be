<?php
namespace Snaptec\Post\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Snaptec\Post\Model\PostFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\File\Mime;

class SavePost implements ResolverInterface
{
    protected $postFactory;
    protected $fileSystem;

    public function __construct(
        PostFactory $postFactory,
        Filesystem $fileSystem
        
    ) {
        $this->postFactory = $postFactory;
        $this->fileSystem = $fileSystem;
    }

    public function resolve(
    Field $field,
    $context,
    ResolveInfo $info,
    array $value = null,
    array $args = null)
    {
        $postId = isset($args['input']['id']) ? $args['input']['id'] : null;
        $title = isset($args['input']['title']) ? $args['input']['title'] : null;
        $content = isset($args['input']['content']) ? $args['input']['content'] : null;
        $base64_image = isset($args['input']['base64_image']) ? $args['input']['base64_image'] : null;
        // Validate required fields
        if (empty($title)) {
            throw new GraphQlInputException(__('Title are required fields.'));
        }
        // Validate required fields
        if (empty($content)) {
            throw new GraphQlInputException(__('Content are required fields.'));
        }
        // Validate required fields
        if (empty($base64_image)) {
            throw new GraphQlInputException(__('Image are required fields.'));
        }
        // Save post data
        $post = $this->postFactory->create();
        // Validate ID
        switch($postId){
            case $postId == null:
            break;
            case $postId == "":
                break;
            case !is_numeric($postId):
                throw new GraphQlNoSuchEntityException(__("Post required is number", $postId));
                break;
            case !($post->load($postId)->getId()):
                throw new \InvalidArgumentException(__("Post with ID %1 does not exist.", $postId));
                break;
        }
        // Process image upload
        $mediaFullPath     = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('post/images/');
        $fileName        = rand() . time();
        $fileContent = base64_decode( $base64_image );
        if (!is_dir($mediaFullPath)) {
            mkdir($mediaFullPath, 0777, true);
        }
        file_put_contents($mediaFullPath . $fileName, $fileContent);
        $imageInfo = getimagesize($mediaFullPath.$fileName);
        if ($imageInfo === false) {
            // Không phải là tệp ảnh hợp lệ
            // Thực hiện xử lý tương ứng
            unlink($mediaFullPath.$fileName); // Xóa tệp tạm
            throw new GraphQlNoSuchEntityException(__("Base64_image is not is image")); 
        }
        if($post->getImage()){
            unlink($mediaFullPath.$post->getImage()); // Xóa anh cu
        }
        $post->setTitle($title);
        $post->setContent($content);
        $post->setImage($fileName);
        $post->save();

        return ['id' => $post->getId(),
        'title'=> $post->getTitle(),
        'content'=> $post->getContent(),
        'image'=> $post->getImage(),
        ];
    }
}
