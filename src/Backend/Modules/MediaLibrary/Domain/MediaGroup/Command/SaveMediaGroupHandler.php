<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Ramsey\Uuid\Uuid;

final class SaveMediaGroupHandler
{
    /** @var MediaItemRepository */
    protected $mediaItemRepository;

    /**
     * CreateMediaGroupHandler constructor.
     *
     * @param MediaItemRepository $mediaItemRepository
     */
    public function __construct(MediaItemRepository $mediaItemRepository)
    {
        $this->mediaItemRepository = $mediaItemRepository;
    }

    /**
     * @param SaveMediaGroup $updateMediaGroup
     */
    public function handle(SaveMediaGroup $updateMediaGroup)
    {
        /** @var MediaGroup $mediaGroup */
        $mediaGroup = MediaGroup::fromDataTransferObject($updateMediaGroup);

        /**
         * @var int $sequence
         * @var string $mediaItemId
         */
        foreach ($updateMediaGroup->mediaItemIdsToConnect as $sequence => $mediaItemId) {
            try {
                /** @var int $newSequence */
                $newSequence = $sequence + 1;

                /** @var MediaItem $mediaItem */
                $mediaItem = $this->mediaItemRepository->getOneById(Uuid::fromString($mediaItemId));

                /** @var MediaGroupMediaItem $mediaGroupMediaItem */
                $mediaGroupMediaItem = MediaGroupMediaItem::create(
                    $mediaGroup,
                    $mediaItem,
                    $newSequence
                );

                $mediaGroup->addConnectedItem($mediaGroupMediaItem);
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        // We redefine the MediaGroup, so we can use it in an action
        $updateMediaGroup->setMediaGroup($mediaGroup);
    }
}
