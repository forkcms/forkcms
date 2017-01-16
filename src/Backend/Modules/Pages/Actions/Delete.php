<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the delete-action, it will delete a page
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendPagesModel::exists($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // init var
            $success = false;

            // cannot have children
            if (BackendPagesModel::getFirstChildId($this->id) !== false) {
                $this->redirect(
                    BackendModel::createURLForAction('Edit') . '&error=non-existing'
                );
            }

            $revisionId = $this->getParameter('revision_id', 'int');
            if ($revisionId == 0) {
                $revisionId = null;
            }

            // get page (we need the title)
            $page = BackendPagesModel::get($this->id, $revisionId);

            // valid page?
            if (!empty($page)) {
                // delete the page
                $success = BackendPagesModel::delete($this->id, null, $revisionId);

                // delete search indexes
                BackendSearchModel::removeIndex($this->getModule(), $this->id);

                // build cache
                BackendPagesModel::buildCache(BL::getWorkingLanguage());
            }

            // page is deleted, so redirect to the overview
            if ($success) {
                $this->redirect(
                    BackendModel::createURLForAction(
                        'Index'
                    ) . '&id=' . $page['parent_id'] . '&report=deleted&var=' . rawurlencode($page['title'])
                );
            } else {
                $this->redirect(BackendModel::createURLForAction('Edit') . '&error=non-existing');
            }
        } else {
            $this->redirect(BackendModel::createURLForAction('Edit') . '&error=non-existing');
        }
    }
}
