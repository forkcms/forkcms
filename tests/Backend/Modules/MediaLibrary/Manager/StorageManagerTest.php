<?php

namespace ForkCMS\Tests\Backend\Modules\MediaLibrary\Manager;

use ForkCMS\Backend\Modules\MediaLibrary\Component\StorageProvider\LocalStorageProvider;
use ForkCMS\Backend\Modules\MediaLibrary\Component\StorageProvider\VimeoStorageProvider;
use ForkCMS\Backend\Modules\MediaLibrary\Component\StorageProvider\YoutubeStorageProvider;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;
use ForkCMS\Backend\Modules\MediaLibrary\Manager\StorageManager;
use PHPUnit\Framework\TestCase;

class StorageManagerTest extends TestCase
{
    /** @var StorageManager */
    protected $storageManager;

    public function setUp(): void
    {
        $this->storageManager = new StorageManager();
    }

    public function testLocalStorageProvider(): void
    {
        $cacheManager = $this->createMock('Liip\ImagineBundle\Imagine\Cache\CacheManager');
        $localStorageProvider = new LocalStorageProvider(
            'src/Frontend/Files/MediaLibrary',
            'https://www.website.be',
            'https://www.website.be',
            $cacheManager
        );

        $this->storageManager->addStorageProvider($localStorageProvider, 'local');
        $this->assertEquals(
            $this->storageManager->getStorageProvider(StorageType::local()),
            $localStorageProvider
        );
    }

    public function testYoutubeStorageProvider(): void
    {
        $youtubeStorageProvider = new YoutubeStorageProvider(
            'https://www.youtube.com/watch?v=',
            'https://www.youtube.com/embed/'
        );
        $this->storageManager->addStorageProvider($youtubeStorageProvider, 'youtube');
        $this->assertEquals(
            $this->storageManager->getStorageProvider(StorageType::youtube()),
            $youtubeStorageProvider
        );
    }

    public function testVimeoStorageProvider(): void
    {
        $vimeoStorageProvider = new VimeoStorageProvider(
            'https://www.vimeo.com/',
            'https://player.vimeo.com/video/'
        );
        $this->storageManager->addStorageProvider($vimeoStorageProvider, 'vimeo');
        $this->assertEquals(
            $this->storageManager->getStorageProvider(StorageType::vimeo()),
            $vimeoStorageProvider
        );
    }
}
