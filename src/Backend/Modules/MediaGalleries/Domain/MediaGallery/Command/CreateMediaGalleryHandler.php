<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryRepository;

final class CreateMediaGalleryHandler
{
    /** @var MediaGalleryRepository */
    private $mediaGalleryRepository;

    /**
     * @param MediaGalleryRepository $mediaGalleryRepository
     */
    public function __construct(
        MediaGalleryRepository $mediaGalleryRepository
    ) {
        $this->mediaGalleryRepository = $mediaGalleryRepository;
    }

    /**
     * @param CreateMediaGallery $createMediaGallery
     */
    public function handle(CreateMediaGallery $createMediaGallery)
    {
        /** @var MediaGallery $mediaGallery */
        $mediaGallery = MediaGallery::fromDataTransferObject($createMediaGallery);
        $this->mediaGalleryRepository->add($mediaGallery);

        // We redefine the mediaGallery, so we can use it in an action
        $createMediaGallery->setMediaGalleryEntity($mediaGallery);
    }
}
