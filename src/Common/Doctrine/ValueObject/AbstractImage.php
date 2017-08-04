<?php

namespace Common\Doctrine\ValueObject;

use Backend\Core\Engine\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * The following things are mandatory to use this class.
 *
 * You need to implement the method getUploadDir.
 * When using this class in an entity certain life cycle callbacks should be called
 * prepareToUpload for PrePersist() and PreUpdate()
 * upload for PostPersist() and PostUpdate()
 * remove for PostRemove()
 *
 * The following things are optional
 * A fallback image can be set by setting the full path of the image to the FALLBACK_IMAGE constant
 * By default we will use the fork way for image sizes (source, 100X100 etc)
 * if you don't want it set GENERATE_THUMBNAILS to false
 */
abstract class AbstractImage extends AbstractFile
{
    /**
     * @var string
     */
    const FALLBACK_IMAGE = '';

    /**
     * When set to true a source directory will be created inside the upload directory
     * Different sizes will be generated based on the folders (old school Fork way)
     *
     * @var bool
     */
    const GENERATE_THUMBNAILS = true;

    public function getAbsolutePath(string $subDirectory = null): ?string
    {
        if (self::GENERATE_THUMBNAILS && $subDirectory === null) {
            $subDirectory = 'source';
        }

        return $this->fileName === null ? null : $this->getUploadRootDir($subDirectory) . '/' . $this->fileName;
    }

    public function getWebPath(string $subDirectory = null): string
    {
        if (self::GENERATE_THUMBNAILS && $subDirectory === null) {
            $subDirectory = 'source';
        }

        $file = $this->getAbsolutePath($subDirectory);

        if (is_file($file) && file_exists($file)) {
            $webPath = FRONTEND_FILES_URL . '/' . $this->getTrimmedUploadDir() . '/';
            if ($subDirectory !== null) {
                $webPath .= $subDirectory . '/';
            }

            return $webPath . $this->fileName;
        }

        return static::FALLBACK_IMAGE;
    }

    protected function getUploadRootDir(string $subDirectory = null): string
    {
        // the absolute directory path where uploaded
        // documents should be saved
        if ($subDirectory !== null) {
            return parent::getUploadRootDir() . '/' . $subDirectory;
        }

        return parent::getUploadRootDir();
    }

    /**
     * This function should be called for the life cycle events PostPersist() and PostUpdate()
     */
    public function upload(): void
    {
        $file = $this->getFile();
        parent::upload();

        if (static::GENERATE_THUMBNAILS && $file instanceof UploadedFile) {
            Model::generateThumbnails(
                FRONTEND_FILES_PATH . '/' . $this->getTrimmedUploadDir(),
                $this->getAbsolutePath('source')
            );
        }
    }

    /**
     * This will remove the old image and if needed the generated thumbnails
     */
    protected function removeOldFile(): void
    {
        if (static::GENERATE_THUMBNAILS && is_dir($this->getUploadRootDir())) {
            Model::deleteThumbnails($this->getUploadRootDir(), $this->oldFileName);

            return;
        }

        parent::removeOldFile();
    }

    /**
     * This function should be called for the life cycle event PostRemove()
     */
    public function remove(): void
    {
        if (static::GENERATE_THUMBNAILS && is_dir($this->getUploadRootDir())) {
            Model::deleteThumbnails($this->getUploadRootDir(), $this->fileName);

            return;
        }

        parent::remove();
    }

    public function getFallbackImage(): string
    {
        return static::FALLBACK_IMAGE;
    }

    protected function writeFileToDisk(): void
    {
        $this->getFile()->move($this->getUploadRootDir(self::GENERATE_THUMBNAILS ? 'source' : null), $this->fileName);
    }
}
