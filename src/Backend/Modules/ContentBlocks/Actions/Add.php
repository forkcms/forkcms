<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\ContentBlock\ContentBlockType;
use Backend\Modules\ContentBlocks\ContentBlock\CreateContentBlock;

/**
 * This is the add-action, it will display a form to create a new item
 */
class Add extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $createContentBlockCommand = new CreateContentBlock();
        $form = $this->createForm(
            new ContentBlockType(
                CreateContentBlock::class,
                $this->get('fork.settings')->get('Core', 'theme', 'core')
            ),
            $createContentBlockCommand
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        $createContentBlockCommand = $form->getData();

        /** @var ContentBlock $contentBlock */
        $contentBlock = $this->get('content_blocks.handler')->create($createContentBlockCommand);

        // trigger fork cms event
        BackendModel::triggerEvent(
            $this->getModule(),
            'after_add',
            [
                'item' => [
                    'id' => $contentBlock->getId(),
                    'user_id' => $contentBlock->getUserId(),
                    'template' => $contentBlock->getTemplate(),
                    'language' => (string) $contentBlock->getLanguage(),
                    'title' => $contentBlock->getTitle(),
                    'text' => $contentBlock->getText(),
                    'hidden' => $contentBlock->isHidden(),
                    'status' => (string) $contentBlock->getStatus(),
                    'created_on' => $contentBlock->getCreatedOn(),
                    'edited_on' => $contentBlock->getEditedOn(),
                    'revision_id' => $contentBlock->getRevisionId(),
                ],
            ]
        );

        return $this->redirect(
            BackendModel::createURLForAction('Index') . '&report=added&var=' .
            rawurlencode($contentBlock->getTitle()) . '&highlight=row-' . $contentBlock->getId()
        );
    }
}
