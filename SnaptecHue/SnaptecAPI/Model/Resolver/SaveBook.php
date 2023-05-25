<?php

declare(strict_types=1);

namespace SnaptecHue\SnaptecAPI\Model\Resolver;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SnaptecHue\SnaptecAPI\Api\BookRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use SnaptecHue\SnaptecAPI\Api\Data\BookInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;

class SaveBook implements ResolverInterface
{
    /**
     * @var BookInterface
     * Object for creating and accessing book data
     */
    protected $bookInterface;

    /**
     * @var UploaderFactory
     * Object for uploading files
     */
    protected $fileUploader;

    /**
     * @var DirectoryList
     * Object representing the list of directories in the application
     */
    private $mediaDirectory;
    /**
     * @var BookRepositoryInterface
     * Object for accessing and managing book data in the repository
     */
    private $bookRepository;
    /**
     * @var BookResource
     * Object for accessing and managing book data
     */
    protected $bookResource;
    
    public function __construct(
        BookRepositoryInterface $bookRepository,
        Filesystem $filesystem,
        BookInterface $bookInterface,
        UploaderFactory $fileUploader,
    ) {
        $this->bookRepository = $bookRepository;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->bookInterface = $bookInterface;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Resolve GraphQL query.
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param mixed|null $value
     * @param mixed|null $args
     * @return array
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            return $this->saveBook($args['input']);
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
    /**
     * Save book data.
     *
     * @param array $data
     * @throws GraphQlInputException
     * @return array
     */
    public function saveBook($data)
    {
        try {
            $imageFile = "";
            $this->vaildateData($data);
            if (isset($data['image'])) {
                $imageFile = $this->uploadFile($data['image']);
                if (!$imageFile) {
                    throw new GraphQlInputException(__('Invalid Image'));
                }
            }
            $bookData = $this->bookInterface->setData([
                "title" => $data['title'],
                "content" => $data['content'],
                "image" => $imageFile
            ]);
            if (isset($data['id'])) {
                $bookData->setId($data['id']);
            }
            $this->bookRepository->saveGraphQL($bookData);
            $response = "save successfully";
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
        return [
            "id" => $bookData->getId(),
            "title" => $bookData->getTitle(),
            "content" => $bookData->getContent(),
            "image" => $bookData->getImage(),
            "message" => $response
        ];
    }

    /**
     * Validate required data.
     *
     * @param array $data
     * @throws LocalizedException
     */
    private function vaildateData($data)
    {
        if (!isset($data['title']) || !isset($data['content']) || !isset($data['image'])) {
            throw new LocalizedException(__('Must be set required data'));
        }
    }
    /**
     * Upload file.
     *
     * @param array $fileData
     * @return string|null
     */
    public function uploadFile($fileData)
    {
        $folderPath = '/book/images/';
        $mediaPath =  $this->mediaDirectory->getAbsolutePath();

        $data = $fileData['base_64_code'];
        $fileExtension = explode('/', mime_content_type($data))[1];
        if (!empty($fileExtension)) {
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $fileFormat = $folderPath . $fileData['name'] . uniqid() . '.' . $fileExtension;
            if (file_put_contents($mediaPath .  $fileFormat, $data)) {
                return $fileFormat;
            } else {
                return null;
            }
        }
    }
}
