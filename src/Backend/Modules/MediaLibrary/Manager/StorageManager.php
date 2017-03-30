<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Component\StorageProvider\StorageProviderInterface;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;

class StorageManager
{
    /** @var array */
    protected $providers = [];

    /**
     * @param StorageProviderInterface $storageProvider
     * @param string $storageType
     * @throws \Exception
     */
    public function addStorageProvider(StorageProviderInterface $storageProvider, string $storageType)
    {
        try {
            $storageType = StorageType::fromString($storageType);
        } catch (\Exception $e) {
            throw $e;
        }

        // Add the storage provider
        $this->providers[$storageType->getStorageType()] = $storageProvider;
    }

    /**
     * @param StorageType $storageType
     * @return StorageProviderInterface
     * @throws \Exception
     */
    public function getStorageProvider(StorageType $storageType): StorageProviderInterface
    {
        if (!array_key_exists($storageType->getStorageType(), $this->providers)) {
            throw new \Exception('MediaLibrary can\'t find any defined StorageProvider for the given storage type: "' . $storageType->getStorageType() . '".');
        }

        return $this->providers[$storageType->getStorageType()];
    }
}
