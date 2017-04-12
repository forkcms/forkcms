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
     * @param SaveMediaGroup $saveMediaGroup
     */
    public function handle(SaveMediaGroup $saveMediaGroup)
    {
        /** @var MediaGroup $mediaGroup */
        $mediaGroup = MediaGroup::fromDataTransferObject($saveMediaGroup);

        /**
         * @var int $sequence
         * @var string $mediaItemId
         */
        foreach ($saveMediaGroup->mediaItemIdsToConnect as $sequence => $mediaItemId) {
            try {
                /** @var MediaItem $mediaItem */
                $mediaItem = $this->mediaItemRepository->findOneById(Uuid::fromString($mediaItemId));

                /** @var MediaGroupMediaItem $mediaGroupMediaItem */
                $mediaGroupMediaItem = MediaGroupMediaItem::create(
                    $mediaGroup,
                    $mediaItem,
                    $sequence
                );

                $mediaGroup->addConnectedItem($mediaGroupMediaItem);
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        // We redefine the MediaGroup, so we can use it in an action
        $saveMediaGroup->setMediaGroup($mediaGroup);
    }
}
