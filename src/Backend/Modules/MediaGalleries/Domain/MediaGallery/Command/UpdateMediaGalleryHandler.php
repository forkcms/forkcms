<?php

namespace ForkCMS\Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use ForkCMS\Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;

final class UpdateMediaGalleryHandler
{
    public function handle(UpdateMediaGallery $updateMediaGallery): void
    {
        // We redefine the mediaGallery, so we can use it in an action
        $updateMediaGallery->setMediaGalleryEntity(MediaGallery::fromDataTransferObject($updateMediaGallery));
    }
}
