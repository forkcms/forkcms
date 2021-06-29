<?php

namespace Frontend\Modules\MediaGalleries\Widgets;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryRepository;
use Frontend\Core\Engine\Base\Widget as BackendBaseWidget;

/**
 * This will show a MediaGallery.
 */
class Gallery extends BackendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();

        /** @var MediaGallery|null $mediaGallery */
        $mediaGallery = $this->getMediaGallery();

        if (!$mediaGallery instanceof MediaGallery) {
            return;
        }

        // Note: we must assign the "widget" first, before assigning variables
        $this->template->assign(
            'widget',
            $this->get('media_library.helper.frontend')->parseWidget(
                $mediaGallery->getAction(),
                $mediaGallery->getMediaGroup()->getId()
            )
        );

        $this->template->assign('mediaGallery', $mediaGallery);
    }

    private function getMediaGallery(): ?MediaGallery
    {
        if (!array_key_exists('gallery_id', $this->data)) {
            return null;
        }

        try {
            /** @var MediaGallery $mediaGallery */
            $mediaGallery = $this->get(MediaGalleryRepository::class)->findOneById($this->data['gallery_id']);
        } catch (\Exception $e) {
            return null;
        }

        // Only return MediaGallery if the gallery should be visible
        return $mediaGallery->isVisible() ? $mediaGallery : null;
    }
}
