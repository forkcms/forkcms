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
use Backend\Modules\ContentBlocks\Command\CreateContentBlock;
use Backend\Modules\ContentBlocks\Form\ContentBlockType;

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

        $form = $this->createForm(
            new ContentBlockType(
                $this->get('fork.settings')->get('Core', 'theme', 'core')
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        /** @var CreateContentBlock $createContentBlock */
        $createContentBlock = $form->getData();

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle($createContentBlock);

        return $this->redirect(
            BackendModel::createURLForAction(
                'Index',
                null,
                null,
                [
                    'report' => 'added',
                    'var' => $createContentBlock->title,
                ]
            )
        );
    }
}
