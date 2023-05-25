<?php

namespace Snaptec\Post\Helper;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Snaptec\Post\Model\ResourceModel\Post\Collection;


class Data
{

    protected $_fileUploaderFactory;
    protected $_filesystem;
    protected $_file;
    protected $_collection;


    public function __construct(
        UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        File $file,
        Collection $collection

    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_file = $file;
        $this->_collection = $collection;
    }

    /**
     * Handle save image
     * @return string
     */
    public function uploadImage()
    {
        try {
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            if (!$uploader->checkMimeType(['image/jpg', 'image/jpeg', 'image/gif', 'image/png'])) {
                throw new Exception(__('File validation failed.'), 422);
            }
            $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('images/');
            $uploader->save($path);
            return $uploader->getUploadedFileName();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Upload image of type base64
     * @return string
     */
    public function uploadImageBase64(string $image)
    {

        try {
            $image = base64_decode($image);
            $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('images/');
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);

            if (!$this->IsCorretTypeImage($mime_type)) {
                throw new Exception(__('File validation failed.'), 422);
            }

            $imageName = time() . '.' . str_replace('image/', '', $mime_type);
            $filePath = $path . $imageName;
            // $this->_file->filePutContents($filePath, $imageName);

            file_put_contents($filePath, $image);

            return $imageName;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Check if two titles are the same
     * @return bool 
     */
    public function isEqualTitle(string $oldTitle, string $newTitle)
    {
        return str_replace(' ', '', strtolower($oldTitle)) === str_replace(' ', '', strtolower($newTitle));
    }

    /**
     * Validate type of image
     * @return bool
     */
    public function IsCorretTypeImage(string $type)
    {
        $listType = ['image/jpg', 'image/jpeg', 'image/gif', 'image/png'];
        return in_array($type, $listType);
    }

    /**
     * Validate request
     * @return Exception
     */
    public function checkEmtyDataOfField(array $data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                throw new Exception("Field " . $key . " is empty", 404);
            }
        }
    }

    /**
     * Delete old image of post
     * @return void
     */
    public function deleteOldImage($image)
    {
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('images/' . $image);
        try {
            if (!$this->_file->isExists($path)) {
                throw new Exception(__("File don't exits"), 404);
            }
            $this->_file->deleteFile($path);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Check title of post is exits
     * @return bool
     */
    public function checkPostAlreadyExits(string $title)
    {
        $items = $this->_collection->getItems();

        foreach ($items as $item) {
            if ($this->isEqualTitle($title, $item->getData()['title'])) {
                return true;
            }
        }
        return false;
    }
}
