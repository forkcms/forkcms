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
        $this->emptyCacheFolders(
            array(
            'src/Frontend/Cache/CachedTemplates/',
            'src/Frontend/Cache/Config/',
            'src/Frontend/Cache/Locale/',
            'src/Frontend/Cache/MinifiedCss/',
            'src/Frontend/Cache/MinifiedJs/',
            'src/Frontend/Cache/Navigation/',
            'src/Frontend/Cache/CompiledTemplates/',
            )
        );
    }

    public function clearBackendCache()
    {
        $this->emptyCacheFolders(
            array(
                'src/Backend/Cache/Analytics/',
                'src/Backend/Cache/Config/',
                'src/Backend/Cache/Cronjobs/',
                'src/Backend/Cache/Locale/',
                'src/Backend/Cache/Mailmotor/',
                'src/Backend/Cache/Navigation/',
                'src/Backend/Cache/CompiledTemplates/',
                'src/Backend/Cache/Logs/',
            )
        );
    }

    public function clearInstallCache()
    {
        $this->emptyCacheFolders(
            array(
                'src/Install/Cache/',
            )
        );
    }

    public function clearAppCache()
    {
        $this->emptyCacheFolders(
            array(
                'app/cache/',
            )
        );
    }

    public function removeParametersFile()
    {
        $this->removeCacheFiles(
            array(
                'app/config/parameters.yml',
            )
        );
    }

    /**
     * Empties the folders in the given array
     *
     * @param array $folders
     */
    public function emptyCacheFolders(array $folders)
    {
        $finder = new Finder;
        $fs = new Filesystem;

        foreach ($folders as $folder) {
            if ($fs->exists($this->rootDir . $folder)) {
                foreach ($finder->files()->in($this->rootDir . $folder) as $file) {
                    $fs->remove($file->getRealPath());
                }
            }
        }
    }

    /**
     * removes the files in the given array
     *
     * @param array $files
     */
    public function removeCacheFiles(array $files)
    {
        $finder = new Finder;
        $fs = new Filesystem;

        foreach ($files as $file) {
            if ($fs->exists($this->rootDir . $file)) {
                $fs->remove($this->rootDir . $file);
            }
        }
    }

    public function invalidateFrontendCache($module = null, $language = null)
    {
        $module = ($module !== null) ? (string) $module : null;
        $language = ($language !== null) ? (string) $language : null;

        // get cache path
        $path = FRONTEND_CACHE_PATH . '/CachedTemplates';

        if (is_dir($path)) {
            // build regular expression
            if ($module !== null) {
                if ($language === null) {
                    $regexp = '/' . '(.*)' . $module . '(.*)_cache\.tpl/i';
                } else {
                    $regexp = '/' . $language . '_' . $module . '(.*)_cache\.tpl/i';
                }
            } else {
                if ($language === null) {
                    $regexp = '/(.*)_cache\.tpl/i';
                } else {
                    $regexp = '/' . $language . '_(.*)_cache\.tpl/i';
                }
            }

            $finder = new Finder();
            $fs = new Filesystem();
            foreach ($finder->files()->name($regexp)->in($path) as $file) {
                $fs->remove($file->getRealPath());
            }
        }
    }
}
