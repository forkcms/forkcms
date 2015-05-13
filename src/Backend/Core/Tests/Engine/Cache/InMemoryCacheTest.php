<?php

namespace Backend\Core\Tests\Engine\Cache;

use Backend\Core\Engine\Cache\InMemoryCache;
use PHPUnit_Framework_TestCase;

/**
 * Testing our in memory cache
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class InMemoryCacheTest extends PHPUnit_Framework_TestCase
{
    public function testCacheDoesNotContainsNotSavedItems()
    {
        $inMemoryCache = new InMemoryCache();

        $this->assertFalse($inMemoryCache->isCached('test'));
        $this->assertFalse($inMemoryCache->isCached(time()));
    }

    public function testSettingAndGettingDataInCache()
    {
        $inMemoryCache = new InMemoryCache();

        $inMemoryCache->cache('test', 'content');
        $this->assertEquals(
            'content',
            $inMemoryCache->getFromCache('test')
        );
    }
}
