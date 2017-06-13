<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockCreated;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use Symfony\Component\Form\Form;

/**
 * This is the add-action, it will display a form to create a new item
 */
class ContentBlockAdd extends BackendBaseActionAdd
{
    public function execute(): void
    {
        parent::execute();

        /** @var Form $form */
        $form = $this->getForm();

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        /** @var CreateContentBlock $createContentBlock */
        $createContentBlock = $this->createContentBlock($form);

        $this->get('event_dispatcher')->dispatch(
            ContentBlockCreated::EVENT_NAME,
            new ContentBlockCreated($createContentBlock->getContentBlockEntity())
        );

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'content-block-added',
                    'var' => $createContentBlock->title,
                ]
            )
        );
    }

    private function createContentBlock(Form $form): CreateContentBlock
    {
        /** @var CreateContentBlock $createContentBlock */
        $createContentBlock = $form->getData();
        $createContentBlock->userId = Authentication::getUser()->getUserId();

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle($createContentBlock);

        return $createContentBlock;
    }

    private function getBackLink(array $parameters = []): string
    {
        return BackendModel::createURLForAction(
            'ContentBlockIndex',
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
            ['theme' => $this->get('fork.settings')->get('Core', 'theme', 'Core')]
        );

        $form->handleRequest($this->get('request'));

        return $form;
    }
}
