<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\DeleteMediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Exception\MediaGalleryNotFound;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;

/**
 * This is the class to Delete a MediaGallery
 */
class MediaGalleryDelete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        parent::execute();

        /** @var MediaGallery $mediaGallery */
        $mediaGallery = $this->getMediaGallery();

        /** @var DeleteMediaGallery $deleteMediaGallery */
        $deleteMediaGallery = new DeleteMediaGallery($mediaGallery);

        // Handle the MediaGallery delete
        $this->get('command_bus')->handle($deleteMediaGallery);

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'media-gallery-deleted',
                    'var' => $deleteMediaGallery->mediaGallery->getTitle(),
                ]
            )
        );
    }

    private function getMediaGallery(): MediaGallery
    {
        try {
            /** @var MediaGallery|null $mediaGallery */
            return $this->get('media_galleries.repository.gallery')->findOneById(
                $this->getParameter('id', 'integer')
            );
        } catch (MediaGalleryNotFound $mediaGalleryNotFound) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'non-existing-media-gallery',
                    ]
                )
            );
        }
    }

    private function getBackLink(array $parameters = []): string
    {
        return Model::createURLForAction(
            'MediaGalleryIndex',
            null,
            null,
            $parameters
        );
    }
}
