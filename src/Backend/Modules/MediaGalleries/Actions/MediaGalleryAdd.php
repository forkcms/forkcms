<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryType;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\CreateMediaGallery;
use InvalidArgumentException;
use Symfony\Component\Form\Form;

/**
 * This is the class to Add a MediaGallery
 */
class MediaGalleryAdd extends ActionAdd
{
    public function execute(): void
    {
        parent::execute();

        /** @var Form $form */
        $form = $this->getForm();

        if (!$form->isValid()) {
            $this->parseForm($form);

            return;
        }

        /** @var CreateMediaGallery $createMediaGallery */
        $createMediaGallery = $this->createMediaGallery($form);

        $this->redirect(
            $this->getBackLink(
                $this->getParametersForCreateMediaGallery($createMediaGallery)
            )
        );
    }

    private function createMediaGallery(Form $form): CreateMediaGallery
    {
        /** @var CreateMediaGallery $createMediaGallery */
        $createMediaGallery = $form->getData();

        // Handle the MediaGallery create
        $this->get('command_bus')->handle($createMediaGallery);

        return $createMediaGallery;
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

    private function getForm(): Form
    {
        $form = $this->createForm(
            MediaGalleryType::class,
            new CreateMediaGallery(
                Authentication::getUser()->getUserId(),
                $this->getMediaGroupType()
            )
        );

        $form->handleRequest($this->get('request'));

        return $form;
    }

    private function getMediaGroupType(): Type
    {
        try {
            return Type::fromString($this->get('request')->query->get('media_group_type')['type']);
        } catch (InvalidArgumentException $e) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'group-type-not-existing',
                    ]
                )
            );
        }
    }

    private function getParametersForCreateMediaGallery(CreateMediaGallery $createMediaGallery): array
    {
        return [
            'report' => 'media-gallery-added',
            'var' => $createMediaGallery->title,
            'highlight' => 'row-' . $createMediaGallery->getMediaGalleryEntity()->getId(),
            'id' => $createMediaGallery->getMediaGalleryEntity()->getId(),
        ];
    }

    private function parseForm(Form $form): void
    {
        $this->tpl->assign('form', $form->createView());
        $this->tpl->assign('backLink', $this->getBackLink());
        $this->tpl->assign('mediaGroup', $form->getData()->mediaGroup);

        // Call parent
        $this->parse();
        $this->display();
    }
}
