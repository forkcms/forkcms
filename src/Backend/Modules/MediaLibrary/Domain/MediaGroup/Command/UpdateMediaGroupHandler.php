<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Ramsey\Uuid\Uuid;

final class UpdateMediaGroupHandler
{
    /** @var MediaItemRepository */
    protected $mediaItemRepository;

    /**
     * CreateMediaGroupHandler constructor.
     *
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(
        MediaItemRepository $mediaItemRepository
    ) {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param UpdateMediaGroup $updateMediaGroup
     */
    public function handle(UpdateMediaGroup $updateMediaGroup)
    {
        // Remove all previous connected items
        if ($updateMediaGroup->removeAllPreviousConnectedMediaItems) {
            $updateMediaGroup->mediaGroup->getConnectedItems()->clear();
        }

        /**
         * @var integer $sequence
         * @var string $mediaItemId
         */
        foreach ($updateMediaGroup->mediaItemIdsToConnect as $sequence => $mediaItemId) {
            try {
                /** @var integer $newSequence */
                $newSequence = $sequence + 1;

                /** @var MediaItem $mediaItem */
                $mediaItem = $this->mediaItemRepository->getOneById(Uuid::fromString($mediaItemId));

                /** @var MediaGroupMediaItem $mediaGroupMediaItem */
                $mediaGroupMediaItem = MediaGroupMediaItem::create(
                    $updateMediaGroup->mediaGroup,
                    $mediaItem,
                    $newSequence
                );

                $updateMediaGroup->mediaGroup->addConnectedItem($mediaGroupMediaItem);
            } catch (\Exception $e) {
                // Do nothing
            }
        }
    }
}
