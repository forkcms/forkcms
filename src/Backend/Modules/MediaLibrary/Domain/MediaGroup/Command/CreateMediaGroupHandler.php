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
        /** @var MediaGroup $mediaGroup */
        $mediaGroup = MediaGroup::fromDataTransferObject($createMediaGroup);
        $this->mediaGroupRepository->add($mediaGroup);
    }
}
