<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Command\DeleteContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockDeleted;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;

/**
 * This is the delete-action, it will delete an item.
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        /** @var ContentBlockRepository $contentBlockRepository */
        $contentBlockRepository = $this->get('content_blocks.repository.content_block');

        $contentBlock = $contentBlockRepository->findOneByIdAndLocale(
            $this->getParameter('id', 'int'),
            Locale::workingLocale()
        );

        if ($contentBlock === null) {
            return $this->redirect(BackendModel::createURLForAction('Index', null, null, ['error' => 'non-existing']));
        }

        // The command bus will handle the saving of the content block in the database.
        $this->get('command_bus')->handle(new DeleteContentBlock($contentBlock));

        $this->get('event_dispatcher')->dispatch(
            ContentBlockDeleted::EVENT_NAME,
            new ContentBlockDeleted($contentBlock)
        );

        return $this->redirect(
            BackendModel::createURLForAction(
                'Index',
                null,
                null,
                [
                    'report' => 'deleted',
                    'var' => $contentBlock->getTitle(),
                ]
            )
        );
    }
}
