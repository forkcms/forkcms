<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryType;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\CreateMediaGallery;

/**
 * This is the class to Add a MediaGallery
 */
class MediaGalleryAdd extends ActionAdd
{
    /**
     * Execute the action
     *
     * @return void
     */
    public function execute()
    {
        parent::execute();

        /** @var Type $mediaGroupType */
        $mediaGroupType = $this->getType();

        $form = $this->createForm(
            new MediaGalleryType(),
            new CreateMediaGallery(
                Authentication::getUser()->getUserId(),
                $mediaGroupType
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());
            $this->tpl->assign('backLink', $this->getBackLink());
            $this->tpl->assign('mediaGroup', $form->getData()->mediaGroup);

            // Call parent
            $this->parse();
            $this->display();

            return;
        }

        /** @var CreateMediaGallery $createMediaGallery */
        $createMediaGallery = $form->getData();

        // Handle the MediaGallery create
        $this->get('command_bus')->handle($createMediaGallery);

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'added-media-gallery',
                    'var' => $createMediaGallery->title,
                    'highlight' => 'row-' . $createMediaGallery->getMediaGalleryEntity()->getId(),
                    'id' => $createMediaGallery->getMediaGalleryEntity()->getId(),
                ]
            )
        );
    }

    /**
     * @return Type
     */
    private function getType()
    {
        try {
            $type = $this->getParameter('type');

            return Type::fromString($type);
        } catch (\InvalidArgumentException $e) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'group-type-not-existing'
                    ]
                )
            );
        }
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function getBackLink(array $parameters = [])
    {
        return Model::createURLForAction(
            'MediaGalleryIndex',
            null,
            null,
            $parameters
        );
    }
}
