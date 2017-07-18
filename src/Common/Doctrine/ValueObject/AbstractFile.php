<?php

namespace Common\Doctrine\ValueObject;

use Common\Uri;
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

    protected function __construct(?string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getAbsolutePath(): ?string
    {
        return $this->fileName === null ? null : $this->getUploadRootDir() . '/' . $this->fileName;
    }

    public function getWebPath(): string
    {
        $file = $this->getAbsolutePath();
        if (is_file($file) && file_exists($file)) {
            return FRONTEND_FILES_URL . '/' . $this->getTrimmedUploadDir() . '/' . $this->fileName;
        }

        return '';
    }

    protected function getUploadRootDir(): string
    {
        return FRONTEND_FILES_PATH . '/' . $this->getTrimmedUploadDir();
    }

    protected function getTrimmedUploadDir(): string
    {
        return trim($this->getUploadDir(), '/\\');
    }

    /**
     * The dir in the web folder where the file needs to be uploaded.
     * The base directory is always the src/Frontend/Files/ directory
     *
     * @return string
     */
    abstract protected function getUploadDir(): string;

    public function setFile(UploadedFile $file = null): self
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
     * @return self
     */
    public static function fromUploadedFile(UploadedFile $uploadedFile = null, string $namePrefix = null): self
    {
        $file = new static(null);
        $file->setFile($uploadedFile);
        if ($namePrefix !== null) {
            $file->setNamePrefix($namePrefix);
        }

        return $file;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    /**
     * This function should be called for the life cycle events PrePersist() and PreUpdate()
     */
    public function prepareToUpload(): void
    {
        if ($this->getFile() === null) {
            return;
        }

        // do whatever you want to generate a unique name
        $filename = sha1(uniqid(mt_rand(), true));
        if ($this->namePrefix !== null) {
            $filename = Uri::getUrl($this->namePrefix) . '_' . $filename;
        }
        $this->fileName = $filename . '.' . $this->getFile()->guessExtension();
    }

    /**
     * This function should be called for the life cycle events PostPersist() and PostUpdate()
     */
    public function upload(): void
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
    protected function removeOldFile(): void
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
    protected function writeFileToDisk(): void
    {
        $this->getFile()->move($this->getUploadRootDir(), $this->fileName);
    }

    /**
     * This function should be called for the life cycle event PostRemove()
     */
    public function remove(): void
    {
        $file = $this->getAbsolutePath();
        if (!is_file($file) || !file_exists($file)) {
            return;
        }

        unlink($file);
    }

    public function __toString(): string
    {
        return (string) $this->fileName;
    }

    public static function fromString(?string $fileName): ?self
    {
        return $fileName !== null ? new static($fileName) : null;
    }

    /**
     * The next time doctrine saves this to the database the file will be removed
     */
    public function markForDeletion(): void
    {
        $this->oldFileName = $this->fileName;
        $this->fileName = null;
    }

    /**
     * @param string $namePrefix If set this will be prepended to the generated filename
     *
     * @return self
     */
    public function setNamePrefix(string $namePrefix): self
    {
        $this->namePrefix = $namePrefix;

        return $this;
    }

    /**
     * @internal Used by the form types
     *
     * @param bool $isPendingDeletion
     */
    public function setPendingDeletion($isPendingDeletion)
    {
        if ($isPendingDeletion) {
            $this->markForDeletion();
        }
    }

    /**
     * @internal Used by the form types
     *
     * @return bool
     */
    public function isPendingDeletion()
    {
        return strlen($this->oldFileName) > 0 && $this->fileName === null;
    }
}
