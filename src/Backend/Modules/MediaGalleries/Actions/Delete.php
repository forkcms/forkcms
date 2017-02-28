<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\DeleteMediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;

/**
 * This is the class to Delete a MediaGallery
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaGallery $mediaGallery */
        $mediaGallery = $this->getMediaGallery();

        /** @var DeleteMediaGallery $deleteMediaGallery */
        $deleteMediaGallery = new DeleteMediaGallery($mediaGallery);

        // Handle the MediaGallery delete
        $this->get('command_bus')->handle($deleteMediaGallery);

        return $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'deleted-media-gallery',
                    'var' => $deleteMediaGallery->mediaGallery->getTitle(),
                ]
            )
        );
    }

    /**
     * @return MediaGallery
     */
    private function getMediaGallery(): MediaGallery
    {
        try {
            /** @var MediaGallery|null $mediaGallery */
            return $this->get('media_galleries.repository.gallery')->getOneById(
                $this->getParameter('id', 'integer')
            );
        } catch (\Exception $e) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'non-existing-media-gallery'
                    ]
                )
            );
        }
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function getBackLink(array $parameters = []): string
    {
        return Model::createURLForAction(
            'Index',
            null,
            null,
            $parameters
        );
    }
}
