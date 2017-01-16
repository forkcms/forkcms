<?php

namespace Backend\Modules\Search\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This action will delete a synonym
 */
class DeleteSynonym extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendSearchModel::existsSynonymById($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get data
            $this->record = (array) BackendSearchModel::getSynonym($this->id);

            // delete item
            BackendSearchModel::deleteSynonym($this->id);

            // item was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Synonyms') . '&report=deleted-synonym&var=' . rawurlencode(
                    $this->record['term']
                )
            );
        } else {
            $this->redirect(BackendModel::createURLForAction('Synonyms') . '&error=non-existing');
        }
    }
}
