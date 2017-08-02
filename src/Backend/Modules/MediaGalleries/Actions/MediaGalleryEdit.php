<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model;
use Backend\Form\Type\DeleteType;
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

        $form->handleRequest($this->getRequest());

        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $mediaGallery->getId()],
            ['module' => $this->getModule(), 'action' => 'MediaGalleryDelete']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());
            $this->template->assign('backLink', $this->getBackLink());
            $this->template->assign('mediaGallery', $mediaGallery);
            $this->template->assign('mediaGroup', $form->getData()->mediaGroup);

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
            /** @var MediaGallery|null $mediaGallery */
            return $this->get('media_galleries.repository.gallery')->findOneById(
                $this->getRequest()->query->get('id')
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
        return Model::createUrlForAction(
            'MediaGalleryIndex',
            null,
            null,
            $parameters
        );
    }
}
