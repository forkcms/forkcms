<?php

namespace Common\Cache;

/**
 * This class can be used to cache properties in the php memory
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
final class InMemoryCache implements Cache
{
    private $cachedData = array();

    /**
     * Checks if the data with a certain name is cached
     *
     * @param  string $cacheName
     * @return boolean
     */
    public function isCached($cacheName)
    {
        return array_key_exists($cacheName, $this->cachedData);
    }

    /**
     * Caches data
     *
     * @param  string $cacheName
     * @param  string $data
     * @return boolean
     */
    public function cache($cacheName, $data)
    {
        $this->cachedData[$cacheName] = $data;
    }

    /**
     * Fetches data from the cache
     *
     * @param  string $cacheName
     * @return string|null
     */
    public function getFromCache($cacheName)
    {
        if ($this->isCached($cacheName)) {
            return $this->cachedData[$cacheName];
        }
    }
}
