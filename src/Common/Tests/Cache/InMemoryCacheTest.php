<?php

namespace Common\Tests\Cache;

use Common\Cache\InMemoryCache;
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

    public function testReturnsNullWhenNotCached()
    {
        $inMemoryCache = new InMemoryCache();

        $this->assertNull($inMemoryCache->getFromCache('test'));
    }
}
