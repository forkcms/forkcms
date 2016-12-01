<?php

namespace Common\Doctrine\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * The following things are mandatory to use this class.
 *
 * You need to implement the method getUploadDir.
 * When using this class in an entity certain life cycle callbacks should be called
 * prepareToUpload for @ORM\PrePersist() and @ORM\PreUpdate()
 * upload for @ORM\PostPersist() and @ORM\PostUpdate()
 * remove for @ORM\PostRemove()
 */
abstract class AbstractFile
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fileName;

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $oldPath;

    /**
     * @param string $fileName
     */
    protected function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string|null
     */
    public function getAbsolutePath()
    {
        return $this->fileName === null ? null : $this->getUploadRootDir() . '/' . $this->fileName;
    }

    /**
     * @return string|null
     */
    public function getWebPath()
    {
        $file = $this->getAbsolutePath();
        if (is_file($file) && file_exists($file)) {
            return FRONTEND_FILES_URL . '/' . $this->getTrimmedUploadDir() . '/' . $this->fileName;
        }

        return;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return FRONTEND_FILES_PATH . '/' . $this->getTrimmedUploadDir();
    }

    /**
     * @return string
     */
    protected function getTrimmedUploadDir()
    {
        return trim($this->getUploadDir(), '/\\');
    }

    /**
     * The dir in the web folder where the file needs to be uploaded.
     * The base directory is always the src/Frontend/Files/ directory
     *
     * @return string
     */
    abstract protected function getUploadDir();

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (!isset($this->fileName)) {
            return;
        }

        // store the old name to delete after the update
        $this->oldPath = $this->fileName;
        $this->fileName = null;
    }

    /**
     * @param UploadedFile|null $uploadedFile
     *
     * @return static
     */
    public static function fromUploadedFile(UploadedFile $uploadedFile = null)
    {
        $file = new static(null);
        $file->setFile($uploadedFile);

        return $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * This function should be called for the life cycle events @ORM\PrePersist() and @ORM\PreUpdate()
     */
    public function prepareToUpload()
    {
        if ($this->getFile() === null) {
            return;
        }

        // do whatever you want to generate a unique name
        $filename = sha1(uniqid(mt_rand(), true));
        $this->fileName = $filename . '.' . $this->getFile()->guessExtension();
    }

    /**
     * This function should be called for the life cycle events @ORM\PostPersist() and @ORM\PostUpdate()
     */
    public function upload()
    {
        if ($this->getFile() === null) {
            return;
        }

        $this->writeFileToDisk();

        // check if we have an old image
        if (isset($this->oldPath)) {
            // delete the old image
            $oldFile = $this->getUploadRootDir() . '/' . $this->oldPath;
            if (is_file($oldFile) && file_exists($oldFile)) {
                unlink($oldFile);
            }
            // clear the $this->oldPath image path
            $this->oldPath = null;
        }
        $this->file = null;
    }

    /**
     * if there is an error when moving the file, an exception will
     * be automatically thrown by move(). This will properly prevent
     * the entity from being persisted to the database on error
     */
    protected function writeFileToDisk()
    {
        $this->getFile()->move($this->getUploadRootDir(), $this->fileName);
    }

    /**
     * This function should be called for the life cycle event @ORM\PostRemove()
     */
    public function remove()
    {
        $file = $this->getAbsolutePath();
        if (!is_file($file) || !file_exists($file)) {
            return;
        }

        unlink($file);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->fileName;
    }

    /**
     * @param string $fileName
     *
     * @return static
     */
    public static function fromString($fileName)
    {
        return new static($fileName);
    }
}
