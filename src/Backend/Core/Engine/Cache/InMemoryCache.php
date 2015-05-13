<?php

namespace Backend\Core\Engine\Cache;

use Symfony\Component\Filesystem\Filesystem;

/**
 * This class can be used to cache properties in the php memory
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
final class InMemoryCache implements Cache
{
    private $data = array();

    /**
     * Checks if the data with a certain name is cached
     *
     * @param  string $cacheName
     * @return boolean
     */
    public function isCached($cacheName)
    {
        return array_key_exists($cacheName, $data);
    }

    /**
     * Caches data
     *
     * @param  string $cacheName
     * @param  string $content
     * @return boolean
     */
    public function cache($cacheName, $data)
    {
        $data[$cacheName] = $data;
    }

    /**
     * Fetches data from the cache
     *
     * @param  string $cacheName
     * @return string
     */
    public function getFromCache($cacheName)
    {
        return $data[$cacheName];
    }
}
