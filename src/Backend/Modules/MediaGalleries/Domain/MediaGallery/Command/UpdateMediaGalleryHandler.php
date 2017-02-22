<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;

final class UpdateMediaGalleryHandler
{
    /**
     * @param UpdateMediaGallery $updateMediaGallery
     */
    public function handle(UpdateMediaGallery $updateMediaGallery)
    {
        /** @var MediaGallery $mediaGallery */
        $mediaGallery = MediaGallery::fromDataTransferObject($updateMediaGallery);

        // We redefine the mediaGallery, so we can use it in an action
        $updateMediaGallery->setMediaGalleryEntity($mediaGallery);
    }
}
