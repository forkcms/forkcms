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
     * Get unique URL
     *
     * @param string $URL
     * @param bool $overwrite
     * @param bool $useImageSharding Image sharding is used by default.
     * @return string
     */
    public function getUniqueURL(
        string $URL,
        bool $overwrite = false,
        bool $useImageSharding = true
    ) : string{
        // define imageShardingFolder
        $imageShardingFolder = '';

        // use image sharding
        if ($useImageSharding) {
            // get id
            $id = $this->settings->get(
                'MediaLibrary',
                'upload_auto_increment',
                0
            ) + 1;

            // define number of sharding folders
            $numberOfShardingFolders = $this->settings->get(
                'MediaLibrary',
                'upload_number_of_sharding_folders',
                15
            );

            // define image sharding folder
            $imageShardingFolder = str_pad(($id % $numberOfShardingFolders), 2, '0', STR_PAD_LEFT);
        }

        // define extension
        $extension = \SpoonFile::getExtension($URL);

        // define name
        $name = (($extension != '') ? str_replace('.' . $extension, '', $URL) : $URL);

        // redefine name as urlised
        $name = Uri::getUrl($name);

        // filename must not be empty
        if (empty($name)) {
            // define random stringname
            $name = BackendModel::generateRandomString(15, true, true, false, false);
        }

        // filename should be unique, don't overwrite existing names
        if (!$overwrite) {
            // Define destinations
            $destinationSourcePath = MediaItem::getUploadRootDir();
            $destinationBackendPath = MediaItem::getUploadRootDir('backend') . '/';

            // Source filename exists
            $this->updateNameIfNotExists(
                $destinationSourcePath,
                $name,
                $extension,
                $imageShardingFolder
            );

            // Thumbnail filename exists
            $this->updateNameIfNotExists(
                $destinationBackendPath,
                $name,
                $extension,
                $imageShardingFolder
            );
        }

        // return
        return $imageShardingFolder . '/' . $name . '.' . $extension;
    }

    /**
     * @param string $targetDir
     * @param string &$name
     * @param string $extension
     * @param string $imageShardingFolder
     */
    private function updateNameIfNotExists(
        string $targetDir,
        string &$name,
        string $extension,
        string $imageShardingFolder = ''
    ) {
        // File with this path exists
        if (\SpoonFile::exists($targetDir . '/' . $imageShardingFolder . '/' . $name . '.' . $extension)) {
            // redefine name to an unique one
            $name = self::getUniqueURLName(
                $targetDir,
                $name,
                $extension,
                $imageShardingFolder
            );
        }
    }

    /**
     * @param string $targetDir
     * @param string $name
     * @param string $extension
     * @param string $imageShardingFolder
     * @return string
     */
    private function getUniqueURLName(
        string $targetDir,
        string $name,
        string $extension,
        string $imageShardingFolder = ''
    ) : string{
        // define some variables
        $count = 1;

        // find unique filename
        while (\SpoonFile::exists(
            $targetDir . $imageShardingFolder . '/' . $name . '_' . $count . '.' . $extension
        )) {
            $count++;
        }

        // redefine name
        $name .= '_' . $count;

        // return new name
        return $name;
    }
}
