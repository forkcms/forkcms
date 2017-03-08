<?php

namespace Frontend\Modules\MediaGalleries\Widgets;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Frontend\Core\Engine\Base\Widget as BackendBaseWidget;

/**
 * This will show a MediaGallery.
 */
class Gallery extends BackendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaGallery|null $mediaGallery */
        $mediaGallery = $this->getMediaGallery();

        /** @var MediaGroup|null $mediaGroup */
        $mediaGroup = ($mediaGallery !== null) ? $mediaGallery->getMediaGroup() : null;

        // We need to have a MediaGroup to show this widget
        if ($mediaGroup !== null) {
            // Assign widget
            // Note: we must assign this first before assigning variables
            $this->tpl->assign(
                'widget',
                // We can create widget for the MediaGroup id
                $this->get('media_library.helper.frontend')->parseWidget(
                    $mediaGallery->getAction(),
                    (string) $mediaGroup->getId()
                )
            );

            // Assign MediaGallery
            $this->tpl->assign('mediaGallery', $mediaGallery);
        }

        $this->loadTemplate();
    }

    /**
     * @return MediaGallery|null
     */
    private function getMediaGallery()
    {
        if (!array_key_exists('gallery_id', $this->data)) {
            return null;
        }

        try {
            /** @var MediaGallery $mediaGallery */
            $mediaGallery = $this->get('media_galleries.repository.gallery')->getOneById(
                $this->data['gallery_id']
            );
        } catch (\Exception $e) {
            return null;
        }

        // Only return MediaGallery if the gallery should be visible
        return ($mediaGallery->isVisible()) ? $mediaGallery : null;
    }
}
