<?php

namespace Frontend\Modules\MediaLibrary\Widgets\Base;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

/**
 * This has some methods to help for our FrontendMediaWidget
 */
class FrontendMediaWidget extends FrontendBaseWidget
{
    /**
     * @var MediaGroup
     */
    protected $mediaGroup;

    protected function loadData(): void
    {
        // We are loading in the MediaGroup for a custom module
        if (isset($this->data['group_id'])) {
            /** @var MediaGroup $mediaGroup */
            $this->mediaGroup = $this->get(MediaGroupRepository::class)->findOneById(
                $this->data['group_id']
            );

            return;
        }

        throw new \Exception(
            'You must define data array with at least a "group_id".'
        );
    }

    protected function parse(): void
    {
        // Add OpenGraph images for Facebook scraper
        $this->get('media_library.helper.frontend')->addOpenGraphImagesForMediaGroup(
            $this->mediaGroup,
            $this->header
        );

        // Assign items (and their source and other custom resolutions)
        $this->template->assign('mediaItems', $this->mediaGroup->getConnectedMediaItems());

        if (isset($this->data['title'])) {
            $this->template->assign(
                'title',
                $this->data['title']
            );
        }
    }
}
