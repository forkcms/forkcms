<?php

namespace ForkCMS\CoreBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class CacheClearer
{
    private $rootDir;

    public function __construct($rootDir)
    {
        // we get the kernel root dir. Our project root dir is one step higher
        $this->rootDir = $rootDir . '/../';
    }

    public function clearFrontendCache()
    {
        $finder = new Finder;
        $fs = new Filesystem;

        $foldersToClear = array(
            'src/Frontend/Cache/CachedTemplates/',
            'src/Frontend/Cache/Config/',
            'src/Frontend/Cache/Locale/',
            'src/Frontend/Cache/MinifiedCss/',
            'src/Frontend/Cache/MinifiedJs/',
            'src/Frontend/Cache/Navigation/',
            'src/Frontend/Cache/CompiledTemplates/',
        );

        foreach ($foldersToClear as $folder) {
            if ($fs->exists($this->rootDir . $folder)) {
                foreach ($finder->files()->in($this->rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }
    }

    public function clearBackendCache()
    {
        $finder = new Finder;
        $fs = new Filesystem;

        $foldersToClear = array(
            'src/Backend/Cache/Analytics/',
            'src/Backend/Cache/Config/',
            'src/Backend/Cache/Cronjobs/',
            'src/Backend/Cache/Locale/',
            'src/Backend/Cache/Mailmotor/',
            'src/Backend/Cache/Navigation/',
            'src/Backend/Cache/CompiledTemplates/',
            'src/Backend/Cache/Logs/',
        );

        foreach ($foldersToClear as $folder) {
            if ($fs->exists($this->rootDir . $folder)) {
                foreach ($finder->files()->in($this->rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }
    }

    public function clearInstallCache()
    {
        $finder = new Finder;
        $fs = new Filesystem;

        $foldersToClear = array(
            'src/Install/Cache/',
        );

        foreach ($foldersToClear as $folder) {
            if ($fs->exists($this->rootDir . $folder)) {
                foreach ($finder->files()->in($this->rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }
    }

    public function clearAppCache()
    {
        $finder = new Finder;
        $fs = new Filesystem;

        $foldersToClear = array(
            'app/cache/',
        );

        foreach ($foldersToClear as $folder) {
            if ($fs->exists($this->rootDir . $folder)) {
                foreach ($finder->files()->in($this->rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }
    }

    public function removeParametersFile()
    {
        $finder = new Finder;
        $fs = new Filesystem;

        $filesToClear = array(
            'app/config/parameters.yml',
        );

        foreach ($filesToClear as $file) {
            if ($fs->exists($this->rootDir . $file)) {
                $fs->remove($this->rootDir . $file);
            }
        }
    }
}
