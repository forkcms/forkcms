<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\UpdateMediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Exception\MediaGalleryNotFound;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryType;

/**
 * This is the class to Edit a MediaGallery
 */
class MediaGalleryEdit extends BackendBaseActionEdit
{
    public function execute(): void
    {
        parent::execute();

        /** @var MediaGallery $mediaGallery */
        $mediaGallery = $this->getMediaGallery();

        $form = $this->createForm(
            MediaGalleryType::class,
            new UpdateMediaGallery(
                $mediaGallery
            ),
            [
                'data_class' => UpdateMediaGallery::class,
            ]
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());
            $this->tpl->assign('backLink', $this->getBackLink());
            $this->tpl->assign('mediaGallery', $mediaGallery);
            $this->tpl->assign('mediaGroup', $form->getData()->mediaGroup);

            // Call parent
            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateMediaGallery $updateMediaGallery */
        $updateMediaGallery = $form->getData();

        // Handle the MediaGallery update
        $this->get('command_bus')->handle($updateMediaGallery);

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'media-gallery-edited',
                    'var' => $updateMediaGallery->title,
                    'highlight' => 'row-' . $updateMediaGallery->getMediaGalleryEntity()->getId(),
                    'id' => $updateMediaGallery->getMediaGalleryEntity()->getId(),
                ]
            )
        );
    }

    private function getMediaGallery(): MediaGallery
    {
        try {
            $id = $this->getParameter('id');

            /** @var MediaGallery|null $mediaGallery */
            return $this->get('media_galleries.repository.gallery')->findOneById($id);
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
