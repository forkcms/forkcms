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
use Backend\Modules\Search\Form\SynonymDeleteType;

/**
 * This action will delete a synonym
 */
class DeleteSynonym extends BackendBaseActionDelete
{
    public function execute(): void
    {
        parent::execute();

        $deleteForm = $this->createForm(SynonymDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Synonyms') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        $id = (int) $deleteFormData['id'];

        if ($id === 0 || !BackendSearchModel::existsSynonymById($id)) {
            $this->redirect(BackendModel::createURLForAction('Synonyms') . '&error=non-existing');
        }

        $synonym = (array) BackendSearchModel::getSynonym($id);
        BackendSearchModel::deleteSynonym($id);

        $this->redirect(
            BackendModel::createURLForAction('Synonyms') . '&report=deleted-synonym&var=' . rawurlencode(
                $synonym['term']
            )
        );
    }
}
