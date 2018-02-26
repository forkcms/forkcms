<?php

namespace ForkCMS\Backend\Modules\ContentBlocks\Actions;

use ForkCMS\Backend\Core\Engine\Authentication;
use ForkCMS\Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockCreated;
use Symfony\Component\Form\Form;

/**
 * This is the add-action, it will display a form to create a new item
 */
class Add extends BackendBaseActionAdd
{
    public function execute(): void
    {
        parent::execute();

        $form = $this->getForm();

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        $createContentBlock = $this->createContentBlock($form);

        $this->get('event_dispatcher')->dispatch(
            ContentBlockCreated::EVENT_NAME,
            new ContentBlockCreated($createContentBlock->getContentBlockEntity())
        );

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'added',
                    'var' => $createContentBlock->title,
                ]
            )
        );
    }

    private function createContentBlock(Form $form): CreateContentBlock
    {
        $createContentBlock = $form->getData();
        $createContentBlock->userId = Authentication::getUser()->getUserId();

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle($createContentBlock);

        return $createContentBlock;
    }

    private function getBackLink(array $parameters = []): string
    {
        return BackendModel::createUrlForAction(
            'Index',
            null,
            null,
            $parameters
        );
    }

    private function getForm(): Form
    {
        $form = $this->createForm(
            ContentBlockType::class,
            new CreateContentBlock(),
            ['theme' => $this->get('fork.settings')->get('Core', 'theme', 'Fork')]
        );

        $form->handleRequest($this->getRequest());

        return $form;
    }
}
