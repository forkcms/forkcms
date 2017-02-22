<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;

final class CreateMediaGroupHandler
{
    /** @var MediaGroupRepository */
    protected $mediaGroupRepository;

    /**
     * CreateMediaGroupHandler constructor.
     *
     * @param MediaGroupRepository $mediaGroupRepository
     */
    public function __construct(
        MediaGroupRepository $mediaGroupRepository
    ) {
        $this->mediaGroupRepository = $mediaGroupRepository;
    }

    /**
     * @param CreateMediaGroup $createMediaGroup
     */
    public function handle(CreateMediaGroup $createMediaGroup)
    {
        if ($createMediaGroup->id === null) {
            /** @var MediaGroup $mediaGroup */
            $mediaGroup = MediaGroup::create(
                $createMediaGroup->type
            );
        } else {
            /** @var MediaGroup $mediaGroup */
            $mediaGroup = MediaGroup::createForId(
                $createMediaGroup->id,
                $createMediaGroup->type
            );
        }

        // Add to the repository
        $this->mediaGroupRepository->add($mediaGroup);

        // Define media group
        $createMediaGroup->setMediaGroup($mediaGroup);
    }
}
