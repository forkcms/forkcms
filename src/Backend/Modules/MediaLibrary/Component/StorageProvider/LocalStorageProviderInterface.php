<?php

namespace App\Backend\Modules\MediaLibrary\Component\StorageProvider;

interface LocalStorageProviderInterface extends StorageProviderInterface, LiipImagineBundleStorageProviderInterface
{
    public function getUploadRootDir(): string;

    public function getWebDir(): string;
}
