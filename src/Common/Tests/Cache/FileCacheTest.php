<?php

namespace Common\Tests\Cache;

use Common\Cache\FileCache;
use PHPUnit_Framework_TestCase;

/**
 * Testing our in memory cache
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class FileCacheTest extends PHPUnit_Framework_TestCase
{
    public function testCacheDoesNotContainsNotSavedItems()
    {
        $fileCache = new FileCache(dirname(__FILE__));

        $this->assertFalse($fileCache->isCached('test'));
        $this->assertFalse($fileCache->isCached(time()));
    }

    public function testSettingAndGettingDataInCache()
    {
        $fileCache = new FileCache(dirname(__FILE__));

        $fileCache->cache('test', 'content');
        $this->assertEquals(
            'content',
            $fileCache->getFromCache('test')
        );

        $this->assertTrue(file_exists(dirname(__FILE__) . '/test'));

        unlink(dirname(__FILE__) . '/test');
    }

    public function testReturnsNullWhenNotCached()
    {
        $fileCache = new FileCache(dirname(__FILE__));

        $this->assertNull($fileCache->getFromCache('test'));
    }
}
