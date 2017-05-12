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
    public function execute(): void
    {
        parent::execute();

        $id = $this->getId();
        $synonym = (array) BackendSearchModel::getSynonym($id);
        BackendSearchModel::deleteSynonym($id);

        $this->redirect(
            BackendModel::createURLForAction('Synonyms') . '&report=deleted-synonym&var=' . rawurlencode(
                $synonym['term']
            )
        );
    }

    private function getId(): int
    {
        $id = $this->getParameter('id', 'int');

        if ($id === 0 || !BackendSearchModel::existsSynonymById($id)) {
            $this->redirect(BackendModel::createURLForAction('Synonyms') . '&error=non-existing');
        }

        return $id;
    }
}
