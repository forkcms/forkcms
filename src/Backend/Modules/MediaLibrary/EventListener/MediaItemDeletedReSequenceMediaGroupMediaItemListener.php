<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use SimpleBus\Message\Bus\MessageBus;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\SaveMediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;

/**
 * When MediaItem is deleted, re-sequence the MediaGroupMediaItem entities.
 */
final class MediaItemDeletedReSequenceMediaGroupMediaItemListener
{
    /** @var MessageBus */
    protected $commandBus;

    /**
     * MediaItemDeletedReSequenceMediaGroupMediaItemListener constructor.
     *
     * @param MessageBus $commandBus
     */
    public function __construct(MessageBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * On MediaItem deleted
     *
     * @param MediaItemDeleted $event
     */
    public function onMediaItemDeleted(MediaItemDeleted $event)
    {
        /** @var ArrayCollection $mediaItemMediaGroups */
        $mediaItemMediaGroups = $event->getMediaItem()->getGroups();

        // This MediaItem has MediaGroups where it is connected to
        if ($mediaItemMediaGroups->count() > 0) {
            // Loop all MediaGroup items
            foreach ($mediaItemMediaGroups as $mediaItemMediaGroup) {
                /** @var MediaGroup $mediaGroup */
                $mediaGroup = $mediaItemMediaGroup->getGroup();

                // Define new media ids
                $newMediaIds = [];

                /**
                 * @var int $index
                 * @var MediaGroupMediaItem $connectedItem
                 */
                foreach ($mediaGroup->getConnectedItems()->toArray() as $index => $connectedItem) {
                    // Add to new media ids
                    $newMediaIds[$index] = $connectedItem->getItem()->getId();
                }

                $updateMediaGroup = new SaveMediaGroup(
                    $mediaGroup,
                    $newMediaIds
                );

                $this->commandBus->handle($updateMediaGroup);
            }
        }
    }
}
