<?php
namespace Snaptec\Post\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Io\File;
class ImageUploader
{
    protected $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
    protected $fileSystem;
    protected $uploaderFactory;
    protected $mediaDirectory;
    protected $file;
    public function __construct(
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        File $file
    ) {
        $this->fileSystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->file = $file;
    }

    public function uploadImage($oldImage,$targetPath)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => 'image']);
        if(isset($uploader)){
            $uploader->setAllowedExtensions($this->allowedExtensions);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            if (!empty($oldImage)) {
                $oldImagePath = $this->mediaDirectory->getAbsolutePath($targetPath) . "/" .$oldImage;
                if ($this->file->fileExists($oldImagePath)) {
                    chmod($oldImagePath,0666);
                    $this->file->rm($oldImagePath);
                }
            }
            $result = $uploader->save($this->mediaDirectory->getAbsolutePath($targetPath));
            
            if (!$result) {
                throw new LocalizedException(__('File cannot be saved to the destination.'));
            }
    
            return $result['file'];
        }
        return null;
    }
}
