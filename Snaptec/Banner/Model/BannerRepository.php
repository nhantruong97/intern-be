<?php

namespace Snaptec\Banner\Model;

use Magento\Framework\Exception\LocalizedException;
use Snaptec\Banner\Model\BannerFactory;
use Snaptec\Banner\Model\ResourceModel\Banner as BannerResource;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

class BannerRepository implements \Snaptec\Banner\Api\BannerInterface
{
    protected $bannerFactory;
    protected $bannerResource;
    protected $uploaderFactory;
    protected $request;
    protected $filesystem;
    protected $file;

    public function __construct(
        BannerFactory $bannerFactory,
        BannerResource $bannerResource,
        UploaderFactory $uploaderFactory,
        Http $request,
        Filesystem $filesystem,
        File $file
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
        $this->uploaderFactory = $uploaderFactory;
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->file = $file;
    }

    /**
     * @inheritdoc
     */
    public function getBanner($id = null, $title, $content)
    {
        try{
            $response = [];
            if (empty($title)) {
                $response['message'] = 'Please enter a title.';
                return $response;
            }

            if (empty($content)) {
                $response['message'] = 'Please enter the Content.';
                return $response;
            }

            switch (true) {
                case empty($id):
                    $banner = $this->bannerFactory->create();
                    $existingBanner = $banner;
                    break;
                case !is_numeric($id):
                    throw new \Exception(__('Invalid %1. %1 must be a numeric value.', $id));
                default:
                    $existingBanner = $this->bannerFactory->create()->load($id);
                    if (empty($existingBanner->getId())) {
                        throw new \Exception(__('The specified %1 does not exist.', $id));
                    }
                    break;
            }
            $existingBanner->setTitle($title);
            $existingBanner->setContent($content);

            $uploader = $this->uploaderFactory->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

            if (!$uploader->validateFile()) {
                throw new LocalizedException(__('Invalid image format.'));
            }

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $result = $uploader->save($mediaDirectory->getAbsolutePath('banner/images'));

            if (!$result) {
                throw new LocalizedException(__('Failed to save image.'));
            }

            $imagePath = 'banner/images/' . $result['file'];

            if ($existingBanner->getId()) {
                $oldImagePath = $existingBanner->getImage();
                $mediaDirectory->delete($oldImagePath);
            }

            $existingBanner->setImage($imagePath);
            $this->bannerResource->save($existingBanner);

            $bannerData = [
                'id' => $existingBanner->getId(),
                'title' => $existingBanner->getTitle(),
                'content' => $existingBanner->getContent(),
                'image' => $existingBanner->getImage(),
            ];

            return [$bannerData];
        }
        catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            // Trả về thông báo
            return ['message' => $errorMessage];
        }
    }
}
