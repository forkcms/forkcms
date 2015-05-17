<?php

namespace Common\Cache;

/**
 * This interface will provide a contract for needed method to cache stuff
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
interface Cache
{
    public function isCached($cacheName);
    public function cache($cacheName, $data);
    public function getFromCache($cacheName);
}
