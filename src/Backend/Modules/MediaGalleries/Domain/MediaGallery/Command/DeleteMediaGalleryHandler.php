<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;

final class DeleteMediaGalleryHandler
{
    /** @var MediaGalleryRepository */
    private $mediaGalleryRepository;

    /** @var MediaItemRepository */
    private $mediaItemRepository;

    /**
     * DeleteMediaGalleryHandler constructor.
     *
     * @param MediaGalleryRepository $mediaGalleryRepository
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(
        MediaGalleryRepository $mediaGalleryRepository,
        MediaItemRepository $mediaItemRepository
    ) {
        $this->mediaGalleryRepository = $mediaGalleryRepository;
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param DeleteMediaGallery $deleteMediaGallery
     */
    public function handle(DeleteMediaGallery $deleteMediaGallery)
    {
        // We should delete all MediaItem entities which were connected to this MediaGallery
        if ($deleteMediaGallery->deleteAllMediaItems) {
            /** @var ArrayCollection|MediaGroupMediaItem $mediaGroupMediaItems */
            $mediaGroupMediaItems = $deleteMediaGallery->mediaGallery->getGroup()->getConnectedItems();

            /** @var MediaGroupMediaItem $mediaGroupMediaItem */
            foreach ($mediaGroupMediaItems->getValues() as $mediaGroupMediaItem) {
                // Delete MediaItem
                $this->mediaItemRepository->remove($mediaGroupMediaItem->getItem());
            }
        }

        $this->mediaGalleryRepository->remove($deleteMediaGallery->mediaGallery);
    }
}
