<?php

namespace Frontend\Modules\MediaLibrary\Widgets\Base;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

/**
 * This has some methods to help for our FrontendMediaWidget
 */
class FrontendMediaWidget extends FrontendBaseWidget
{
    /**
     * @var array
     */
    protected $resolutions = array();

    /**
     * @var MediaGroup
     */
    protected $mediaGroup;

    /**
     * Execute the extra
     */
    public function execute()
    {
        // Will probably add some CSS/JS to the header
        parent::execute();
    }

    /**
     * Load data
     */
    protected function loadData()
    {
        // We are loading in the MediaGroup for a custom module
        if (isset($this->data['group_id'])) {
            /** @var MediaGroup $mediaGroup */
            $this->mediaGroup = $this->get('media_library.repository.group')->getOneById(
                $this->data['group_id']
            );
        } else {
            throw new \Exception(
                'You must define data array with at least a "group_id".'
            );
        }
    }

    /**
     * Parse
     */
    protected function parse()
    {
        // Add OpenGraph images for Facebook scraper
        $this->get('media_library.helper.frontend')->addOpenGraphImagesForMediaGroup(
            $this->mediaGroup,
            $this->header
        );

        // Assign items (and their source and other custom resolutions)
        $this->tpl->assign(
            'items',
            $this->get('media_library.helper.frontend')->createFrontendMediaItems(
                $this->mediaGroup,
                $this->getResolutions()
            )
        );

        if (isset($this->data['title'])) {
            $this->tpl->assign(
                'title',
                $this->data['title']
            );
        }
    }

    /**
     * Get resolutions
     *
     * @return array
     */
    public function getResolutions(): array
    {
        return $this->resolutions;
    }

    /**
     * Set resolutions
     *
     * @param array $resolutions
     */
    public function setResolutions(array $resolutions)
    {
        $this->resolutions = $resolutions;
    }
}
