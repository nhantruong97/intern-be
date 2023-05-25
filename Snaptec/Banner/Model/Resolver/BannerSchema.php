<?php
namespace Snaptec\Banner\Model\Resolver;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\GraphQlInputException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Snaptec\Banner\Model\BannerFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;

class BannerSchema implements ResolverInterface
{
    protected $bannerFactory;
    protected $uploaderFactory;
    protected $filesystem;
    protected $file;
    protected $mediaDirectory;

    public function __construct(
        BannerFactory $bannerFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        File $file,
        DirectoryList $directoryList
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->mediaDirectory = $filesystem->getDirectoryRead($directoryList::MEDIA);
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
            $id = $args['input']['id'] ?? null;
            $title = $args['input']['title'] ?? null;
            $content = $args['input']['content'] ?? null;
    
            switch (true) {
                case empty($title):
                    throw new \Exception(__('Please enter a title.'));
    
                case empty($content):
                    throw new \Exception(__('Please enter the content.'));
    
                case empty($id):
                    $banner = $this->bannerFactory->create();
                    $banner->setTitle($title);
                    $banner->setContent($content);
                    break;
    
                default:
                    if (!is_numeric($id)) {
                        throw new \Exception(__('Invalid ID.'));
                    }
    
                    $banner = $this->bannerFactory->create()->load($id);
                    if (!$banner->getId()) {
                        throw new \Exception(__('The specified %1 does not exist.', $id));
                    }
    
                    $banner->setTitle($title);
                    $banner->setContent($content);
                    break;
            }
    
            if (isset($args['input']['image'])) {
                $image = $args['input']['image'];
                $base64Image = $image['base64_image'];
    
                $imageContent = base64_decode($base64Image);
                $mediaPath = $this->mediaDirectory->getAbsolutePath('banner/images/');
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imageContent, FILEINFO_MIME_TYPE);

                $imageName = time() . '.' . str_replace('image/', '', $mime_type);
                $filePath = $mediaPath . $imageName;
    
                $oldImagePath = $banner->getImage();
                if ($oldImagePath) {
                    $oldFilePath = $this->mediaDirectory->getAbsolutePath($oldImagePath);
                    $this->file->deleteFile($oldFilePath);
                }
    
                $this->file->filePutContents($filePath, $imageContent);
                $imageUrl = 'banner/images/' . $imageName;
    
                $banner->setImage($imageUrl);
            }
    
            $banner->save();
    
            return [
                'id' => $banner->getId(),
                'title' => $banner->getTitle(),
                'content' => $banner->getContent(),
                'image_url' => $banner->getImage()
            ];

    }
}
