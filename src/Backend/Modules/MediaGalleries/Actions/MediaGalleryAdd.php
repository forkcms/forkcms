<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryType;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\CreateMediaGallery;
use Symfony\Component\Form\Form;

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

        /** @var Form $form */
        $form = $this->getForm();

        if (!$form->isValid()) {
            $this->parseForm($form);

            return;
        }

        /** @var CreateMediaGallery $createMediaGallery */
        $createMediaGallery = $form->getData();

        // Handle the MediaGallery create
        $this->get('command_bus')->handle($createMediaGallery);

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'media-gallery-added',
                    'var' => $createMediaGallery->title,
                    'highlight' => 'row-' . $createMediaGallery->getMediaGalleryEntity()->getId(),
                    'id' => $createMediaGallery->getMediaGalleryEntity()->getId(),
                ]
            )
        );
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function getBackLink(array $parameters = []): string
    {
        return Model::createURLForAction(
            'MediaGalleryIndex',
            null,
            null,
            $parameters
        );
    }

    /**
     * @return Form
     */
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

    /**
     * @return Type
     */
    private function getMediaGroupType(): Type
    {
        try {
            return Type::fromString($this->get('request')->query->get('media_group_type')['type']);
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
     * @param Form $form
     */
    private function parseForm(Form $form)
    {
        $this->tpl->assign('form', $form->createView());
        $this->tpl->assign('backLink', $this->getBackLink());
        $this->tpl->assign('mediaGroup', $form->getData()->mediaGroup);

        // Call parent
        $this->parse();
        $this->display();
    }
}
