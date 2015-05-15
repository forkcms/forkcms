<?php

namespace Common\Cache;

use Symfony\Component\Filesystem\Filesystem;

/**
 * This class can be used to cache properties in the cache
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
final class FileCache implements Cache
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Creates a FileCache instance
     *
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        $this->filesystem = new Filesystem();
    }

    /**
     * Checks if the file with a certain name is cached
     *
     * @param  string $cacheName
     * @return boolean
     */
    public function isCached($cacheName)
    {
        return $this->filesystem->exists($this->cacheDir . '/' . $cacheName);
    }

    /**
     * Caches a certain file
     *
     * @param  string $cacheName
     * @param  string $data
     * @return boolean
     */
    public function cache($cacheName, $data)
    {
        return $this->filesystem->dumpFile(
            $this->cacheDir . '/' . $cacheName,
            $data
        );
    }

    /**
     * Reads data from the cache
     *
     * @param  string $cacheName
     * @return string|null
     */
    public function getFromCache($cacheName)
    {
        if ($this->isCached($cacheName)) {
            return file_get_contents($this->cacheDir . '/' . $cacheName);
        }
    }
}
