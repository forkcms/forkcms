<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\SaveMediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;

/**
 * When MediaItem is deleted, re-sequence the MediaGroupMediaItem entities.
 */
final class MediaItemDeletedReSequenceMediaGroupMediaItemListener
{
    /** @var MessageBus */
    protected $commandBus;

    /**
     * @param MessageBus $commandBus
     */
    public function __construct(MessageBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if (!$entity instanceof MediaItem) {
            return;
        }

        /** @var ArrayCollection $mediaItemMediaGroups */
        $mediaItemMediaGroups = $entity->getGroups();

        if ($mediaItemMediaGroups->count() == 0) {
            return;
        }

        // Loop all MediaGroup items
        foreach ($mediaItemMediaGroups as $mediaItemMediaGroup) {
            $this->updateMediaGroupSequence($mediaItemMediaGroup->getGroup());
        }
    }

    /**
     * @param MediaGroup $mediaGroup
     */
    private function updateMediaGroupSequence(MediaGroup $mediaGroup)
    {
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
