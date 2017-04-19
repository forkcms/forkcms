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
        $this->updateConnectedItems($mediaGroup, $saveMediaGroup->mediaItemIdsToConnect);

        // We redefine the MediaGroup, so we can use it in an action
        $saveMediaGroup->setMediaGroup($mediaGroup);
    }

    /**
     * @param MediaGroup $mediaGroup
     * @param array $mediaItemIdsToConnect
     */
    private function updateConnectedItems(MediaGroup $mediaGroup, array $mediaItemIdsToConnect)
    {
        /**
         * @var int $sequence
         * @var string $mediaItemId
         */
        foreach ($mediaItemIdsToConnect as $sequence => $mediaItemId) {
            try {
                $mediaGroup->addConnectedItem(MediaGroupMediaItem::create(
                    $mediaGroup,
                    $this->mediaItemRepository->findOneById(Uuid::fromString($mediaItemId)),
                    $sequence
                ));
            } catch (\Exception $e) {
                // Do nothing
            }
        }
    }
}
