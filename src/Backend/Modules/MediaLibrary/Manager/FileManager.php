<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Symfony\Component\Filesystem\Filesystem;
use Common\ModulesSettings;
use Common\Uri;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Component\ImageSettings;

class FileManager
{
    /** @var ModulesSettings */
    private $settings;

    /** @var Filesystem */
    private $filesystem;

    /**
     * FileManager constructor.
     *
     * @param ModulesSettings $settings
     */
    public function __construct(
        ModulesSettings $settings
    ) {
        $this->settings = $settings;
        $this->filesystem = new Filesystem();
    }

    /**
     * @param string $path
     */
    public function createFolder(string $path)
    {
        if (!is_dir($path)) {
            $this->filesystem->mkdir($path);
        }
    }

    /**
     * Delete file
     *
     * @param string $path
     */
    public function deleteFile(string $path)
    {
        if (is_file($path)) {
            $this->filesystem->remove($path);
        }
    }

    /**
     * Delete folder
     *
     * @param string $path
     */
    public function deleteFolder(string $path)
    {
        if (is_dir($path)) {
            $this->filesystem->remove($path);
        }
    }

    /**
     * Generate thumbnail
     *
     * @param string $fileName
     * @param string $sourcePath
     * @param string $destinationPath
     * @param ImageSettings $imageSettings
     * @throws \Exception
     */
    public function generateThumbnail(
        string $fileName,
        string $sourcePath,
        string $destinationPath,
        ImageSettings $imageSettings
    ) {
        try {
            // Destination folder should exist
            if (!is_dir($destinationPath)) {
                $this->filesystem->mkdir($destinationPath);
            }

            // Define thumbnail
            $thumbnail = new \SpoonThumbnail(
                $sourcePath . '/' . $fileName,
                $imageSettings->getWidth(),
                $imageSettings->getHeight(),
                false
            );

            // Set allow enlargement
            $thumbnail->setAllowEnlargement(true);

            // if the width & height are specified we should ignore the aspect ratio
            if ($imageSettings->getWidth() !== null && $imageSettings->getHeight() !== null) {
                $thumbnail->setForceOriginalAspectRatio(
                    !$imageSettings->getTransformationMethod()->isCrop()
                );
            }

            // Set crop position
            $thumbnail->setCropPosition(
                $imageSettings->getTransformationMethod()->getHorizontalCropPosition(),
                $imageSettings->getTransformationMethod()->getVerticalCropPosition()
            );

            // Parse thumbnail
            $thumbnail->parseToFile(
                $destinationPath . '/' . $fileName,
                $imageSettings->getQuality()
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $targetDir
     * @param string $name
     * @param string $extension
     * @return string
     */
    private function generateUniqueFileName(
        string $targetDir,
        string $name,
        string $extension
    ) : string {
        // define some variables
        $count = 1;

        // find unique filename
        while ($this->filesystem->exists(
            $targetDir . '/' . $name . '_' . $count . '.' . $extension
        )) {
            $count++;
        }

        // redefine name
        $name .= '_' . $count;

        // return new name
        return $name . '.' . $extension;
    }

    /**
     * @return string
     */
    public function getNextShardingFolder(): string
    {
        // get id
        $id = $this->settings->get('MediaLibrary', 'upload_auto_increment', 0) + 1;

        // define number of sharding folders
        $numberOfShardingFolders = $this->settings->get(
            'MediaLibrary',
            'upload_number_of_sharding_folders',
            15
        );

        // define image sharding folder
        return str_pad(($id % $numberOfShardingFolders), 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param $directory
     * @param $fileName
     * @return string
     */
    public function getUniqueFileName(
        $directory,
        $fileName
    ) : string {
        $pathInfo = pathinfo($directory . '/' . $fileName);
        $name = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        // redefine name as urlised
        $name = Uri::getUrl($name);

        // filename must not be empty
        if (empty($name)) {
            // define random stringname
            $name = BackendModel::generateRandomString(15, true, true, false, false);
        }

        if (!$this->filesystem->exists($directory . '/' . $name . '.' . $extension)) {
            return $name . '.' . $extension;
        }

        return $this->generateUniqueFileName($directory, $name, $extension);
    }
}
