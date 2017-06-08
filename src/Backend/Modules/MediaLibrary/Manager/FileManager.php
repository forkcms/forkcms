<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Symfony\Component\Filesystem\Filesystem;
use Common\ModulesSettings;
use Common\Uri;
use Backend\Core\Engine\Model as BackendModel;

final class FileManager
{
    /** @var Filesystem */
    private $filesystem;

    /** @var ModulesSettings|\stdClass */
    private $settings;

    /**
     * FileManager constructor.
     *
     * @param ModulesSettings|\stdClass $settings
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->filesystem = new Filesystem();
    }

    public function createFolder(string $path): void
    {
        if (!$this->exists($path)) {
            $this->filesystem->mkdir($path);
        }
    }

    public function deleteFile(string $path): void
    {
        if ($this->exists($path)) {
            $this->filesystem->remove($path);
        }
    }

    public function deleteFolder(string $path): void
    {
        if ($this->exists($path)) {
            $this->filesystem->remove($path);
        }
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    private function generateUniqueFileName(
        string $targetDir,
        string $name,
        string $extension
    ): string {
        $count = 1;

        // find unique filename
        while ($this->filesystem->exists($targetDir . '/' . $name . '_' . $count . '.' . $extension)) {
            ++$count;
        }

        // redefine name
        $name .= '_' . $count;

        // return new name
        return $name . '.' . $extension;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getNextShardingFolder(): string
    {
        $numberOfShardingFolders = ($this->settings instanceof ModulesSettings)
            ? $this->settings->get('MediaLibrary', 'upload_number_of_sharding_folders', 15) : 15;

        $id = random_int(0, $numberOfShardingFolders);

        // Image sharding folder should look like "01", "02", "10", ...
        return str_pad($id % $numberOfShardingFolders, 2, '0', STR_PAD_LEFT);
    }

    public function getUniqueFileName(string $directory, string $fileName): string
    {
        $pathInfo = pathinfo($directory . '/' . $fileName);
        $name = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        // redefine name as urlised
        $name = Uri::getUrl($name);

        // filename must not be empty
        if (empty($name)) {
            // redefine name with random string
            $name = BackendModel::generateRandomString(15, true, true, false, false);
        }

        if (!$this->filesystem->exists($directory . '/' . $name . '.' . $extension)) {
            return $name . '.' . $extension;
        }

        return $this->generateUniqueFileName($directory, $name, $extension);
    }

    public function rename(string $oldName, string $newName): void
    {
        $this->filesystem->rename($oldName, $newName);
    }
}
