<?php

namespace SnaptecHue\SnaptecAPI\Model;

use Magento\Framework\Filesystem;
use SnaptecHue\SnaptecAPI\Api\Data\BookInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SnaptecHue\SnaptecAPI\Api\BookRepositoryInterface;
use SnaptecHue\SnaptecAPI\Model\ResourceModel\Book as BookResource;
use SnaptecHue\SnaptecAPI\Model\BookFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Message\ManagerInterface;
use SnaptecHue\SnaptecAPI\Model\Cache;
use Psr\Log\LoggerInterface;

class BookRepository implements BookRepositoryInterface
{
    /**
     * @var int
     * Maximum file size in bytes (5MB)
     */
    const MAX_FILE_SIZE = 5242880;

    /**
     * @var Filesystem
     * Object for working with the file system
     */
    protected $filesystem;

    /**
     * @var Book
     * Object representing a book
     */
    protected $book;

    /**
     * @var BookResource
     * Object for accessing and managing book data
     */
    protected $bookResource;

    /**
     * @var FileUploader
     * Object for uploading files
     */
    protected $fileUploader;

    /**
     * @var Directory
     * Object for working with the media directory in Magento
     */
    protected $mediaDirectory;

    /**
     * @var MessageManager
     * Object for managing messages
     */
    protected $messageManager;
    /**
     * @var Cache
     * Cache for api
     */
    protected $cache;
    /**
     * @var LoggerInterface
     * logger debug
     */
    protected $logger;
    /**
     * @var BookFactory
     */
    private $bookFactory;

    /**
     * BookRepository Constructor.
     *
     * @param Filesystem $filesystem 
     * @param BookInterface $book
     * @param BookResource $bookResource
     * @param BookFactory $bookFactory
     * @param UploaderFactory $fileUploader
     * @param ManagerInterface $messageManager
     * @param Cache $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        BookInterface $book,
        BookResource $bookResource,
        BookFactory $bookFactory,
        UploaderFactory $fileUploader,
        ManagerInterface $messageManager,
        Cache $cache,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->book = $book;
        $this->bookResource = $bookResource;
        $this->bookFactory = $bookFactory;
        $this->fileUploader = $fileUploader;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->messageManager = $messageManager;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * Save book.
     *
     * @param \SnaptecHue\SnaptecAPI\Api\Data\BookInterface $book
     * @return \SnaptecHue\SnaptecAPI\Api\Data\BookInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BookInterface $book)
    {
        try {
            if ($book->getId()) {
                // $this->logger->info('book info', ['id' => $book->getId()]);
                $book = $this->getById($book->getId())->addData($book->getData());
            }
            if (isset($_FILES['image']['name']) && isset($_FILES['image']['tmp_name'])) {
                $image = $_FILES['image'];

                //process image file
                $fileName = $this->uploadFile($image);
                // $this->logger->info('book info', ['imagePath' => $fileName]);
                $book->setImage($fileName);
            }
            $this->bookResource->save($book);
        } catch (\Exception $ex) {
            throw new CouldNotSaveException(__('Could not save the book.' . $ex));
        }
        return $book;
    }
    /**
     * Saves a book using GraphQL API.
     *
     * @param BookInterface $book The book object to be saved
     * @return BookInterface The saved book object
     * @throws CouldNotSaveException If the book could not be saved
     */
    public function saveGraphQL(BookInterface $book)
    {
        if ($book->getId()) {
            $book = $this->getById($book->getId())->addData($book->getData());
        }
        try {
            $this->bookResource->save($book);
        } catch (\Exception $ex) {
            throw new CouldNotSaveException(__('Could not save the book.' . $ex));
        }
        return $book;
    }
    /**
     * @inheritdoc
     */
    public function getById($bookId)
    {
        $cacheKey = 'book_' . $bookId;
        $cachedData = $this->cache->load($cacheKey);

        if ($cachedData) {
            // Dữ liệu đã được lưu trong cache, trả về dữ liệu từ cache
            dd('cache');
            return unserialize($cachedData);
        }

        // Dữ liệu không tồn tại trong cache, thực hiện các thao tác lấy dữ liệu từ cơ sở dữ liệu
        $book = $this->bookFactory->create();
        $this->bookResource->load($book, $bookId);

        if (!$book->getId()) {
            throw new NoSuchEntityException(__('Book with specified ID "%1" not found.', $bookId));
        }

        // Lưu dữ liệu vào cache
        $cacheLifetime = 3600;
        $this->cache->save(serialize($book), $cacheKey, [], $cacheLifetime);
        return $book;
    }
    /**
     * Uploads a file.
     *
     * @param array $image 
     * @return string|bool
     */
    public function uploadFile($image)
    {
        // Define the folder name where the file will be uploaded, relative to the "pub/media" folder
        $folderName = '/book/images/';
        try {
            // Check if image data and file name exist
            $fileName = ($image && array_key_exists('name', $image)) ? $image['name'] : null;
            if ($image && $fileName) {
                // Set the target directory
                $target = $this->mediaDirectory->getAbsolutePath($folderName);

                // Create the file uploader instance
                $uploader = $this->fileUploader->create(['fileId' => 'image']);
                $uploader->validateFile();
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'webp', 'png', 'gif']);
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($target);
                if ($result['file']) {
                    $this->messageManager->addSuccess(__('File has been successfully uploaded.'));
                }
                // Return the uploaded file path
                return $folderName . $uploader->getUploadedFileName();
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return false;
    }
}
