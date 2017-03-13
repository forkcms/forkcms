<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Component\StorageProvider\LocalStorageProvider;
use Backend\Modules\MediaLibrary\Component\StorageProvider\StorageProviderInterface;
use Backend\Modules\MediaLibrary\Component\StorageProvider\VimeoStorageProvider;
use Backend\Modules\MediaLibrary\Component\StorageProvider\YoutubeStorageProvider;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;

class StorageManager
{
    /** @var StorageProviderInterface */
    protected $externalStorageProvider;

    /** @var StorageProviderInterface */
    protected $localStorageProvider;

    /** @var StorageProviderInterface */
    protected $youtubeStorageProvider;

    /** @var StorageProviderInterface */
    protected $vimeoStorageProvider;

    /**
     * StorageManager constructor.
     *
     * @param LocalStorageProvider $localStorageProvider
     * @param YoutubeStorageProvider $youtubeStorageProvider
     * @param VimeoStorageProvider $vimeoStorageProvider
     */
    public function __construct(
        LocalStorageProvider $localStorageProvider,
        YoutubeStorageProvider $youtubeStorageProvider,
        VimeoStorageProvider $vimeoStorageProvider
    ) {
        $this->localStorageProvider = $localStorageProvider;
        $this->youtubeStorageProvider = $youtubeStorageProvider;
        $this->vimeoStorageProvider = $vimeoStorageProvider;
    }

    /**
     * @param StorageType $storageType
     * @return StorageProviderInterface
     * @throws \Exception
     */
    public function getStorage(StorageType $storageType): StorageProviderInterface
    {
        switch ($storageType) {
            case StorageType::external():
                if (!$this->hasExternalStorageProvider()) {
                    throw new \Exception('You must define an external storage provider before you can call it.');
                }
                return $this->externalStorageProvider;
                break;
            case StorageType::vimeo():
                return $this->vimeoStorageProvider;
                break;
            case StorageType::youtube():
                return $this->youtubeStorageProvider;
                break;
            default:
                return $this->localStorageProvider;
                break;
        }
    }

    /**
     * @return bool
     */
    public function hasExternalStorageProvider(): bool
    {
        return $this->externalStorageProvider instanceof StorageProviderInterface;
    }

    /**
     * @param StorageProviderInterface $externalStorageProvider
     */
    public function setExternalStorageProvider(StorageProviderInterface $externalStorageProvider)
    {
        $this->externalStorageProvider = $externalStorageProvider;
    }
}
