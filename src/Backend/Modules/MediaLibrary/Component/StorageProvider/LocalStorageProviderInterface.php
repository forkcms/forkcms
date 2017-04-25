<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

interface LocalStorageProviderInterface extends StorageProviderInterface, LiipImagineBundleStorageProviderInterface
{
    /**
     * @return string
     */
    public function getUploadRootDir(): string;

    /**
     * @return string
     */
    public function getWebDir(): string;
}
