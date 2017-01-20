<?php

namespace Common\Doctrine\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * The following things are mandatory to use this class.
 *
 * You need to implement the method getUploadDir.
 * When using this class in an entity certain life cycle callbacks should be called
 * prepareToUpload for PrePersist() and PreUpdate()
 * upload for PostPersist() and PostUpdate()
 * remove for PostRemove()
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
    protected $oldFileName;

    /**
     * @var string
     */
    protected $namePrefix;

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
     * @return string
     */
    public function getWebPath()
    {
        $file = $this->getAbsolutePath();
        if (is_file($file) && file_exists($file)) {
            return FRONTEND_FILES_URL . '/' . $this->getTrimmedUploadDir() . '/' . $this->fileName;
        }

        return '';
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
     * @param UploadedFile|null $file
     *
     * @return static
     */
    public function setFile(UploadedFile $file = null)
    {
        if ($file === null) {
            return $this;
        }

        $this->file = $file;

        // check if we have an old image path
        if ($this->fileName === null) {
            return $this;
        }

        // store the old name to delete after the update
        $this->oldFileName = $this->fileName;
        $this->fileName = null;

        return clone $this;
    }

    /**
     * @param UploadedFile|null $uploadedFile
     * @param string|null $namePrefix If set this will be prepended to the generated filename
     *
     * @return static
     */
    public static function fromUploadedFile(UploadedFile $uploadedFile = null, $namePrefix = null)
    {
        $file = new static(null);
        $file->setFile($uploadedFile);
        $file->setNamePrefix($namePrefix);

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
     * This function should be called for the life cycle events PrePersist() and PreUpdate()
     */
    public function prepareToUpload()
    {
        if ($this->getFile() === null) {
            return;
        }

        // do whatever you want to generate a unique name
        $filename = urlencode($this->namePrefix) . '_' . sha1(uniqid(mt_rand(), true));
        $this->fileName = $filename . '.' . $this->getFile()->guessExtension();
    }

    /**
     * This function should be called for the life cycle events PostPersist() and PostUpdate()
     */
    public function upload()
    {
        // check if we have an old image
        if ($this->oldFileName !== null) {
            $this->removeOldFile();
        }

        if ($this->getFile() === null) {
            return;
        }

        $this->writeFileToDisk();

        $this->file = null;
    }

    /**
     * This will remove the old file, can be extended to add extra functionality
     */
    protected function removeOldFile()
    {
        // delete the old file
        $oldFile = $this->getUploadRootDir() . '/' . $this->oldFileName;
        if (is_file($oldFile) && file_exists($oldFile)) {
            unlink($oldFile);
        }

        $this->oldFileName = null;
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
     * This function should be called for the life cycle event PostRemove()
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
     * Returns a string representation of the image.
     *
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

    /**
     * The next time doctrine saves this to the database the file will be removed
     */
    public function markForDeletion()
    {
        $this->oldFileName = $this->fileName;
        $this->fileName = null;
    }

    /**
     * @param string $namePrefix If set this will be prepended to the generated filename
     * @return self
     */
    public function setNamePrefix($namePrefix)
    {
        $this->namePrefix = $namePrefix;

        return $this;
    }
}
