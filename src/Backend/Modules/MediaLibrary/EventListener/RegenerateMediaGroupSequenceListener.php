<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use SimpleBus\Message\Bus\MessageBus;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\UpdateMediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;

/**
 * Regenerate MediaGroup sequence Listener
 */
final class RegenerateMediaGroupSequenceListener
{
    /** @var MessageBus */
    protected $commandBus;

    /**
     * RegenerateMediaGroupSequenceListener constructor.
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
                $mediaGroup = $mediaItemMediaGroup->getMediaGroup();

                // Define new media ids
                $newMediaIds = array();

                /**
                 * @var int $index
                 * @var MediaGroupMediaItem $connectedItem
                 */
                foreach ($mediaGroup->getConnectedItems()->toArray() as $index => $connectedItem) {
                    // Add to new media ids
                    $newMediaIds[$index] = $connectedItem->getItem()->getId();
                }

                $updateMediaGroup = new UpdateMediaGroup(
                    $mediaGroup,
                    $newMediaIds
                );

                $this->commandBus->handle($updateMediaGroup);
            }
        }
    }
}
