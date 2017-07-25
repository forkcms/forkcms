<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This action is used to update one or more data items
 */
class MassDataAction extends BackendBaseAction
{
    public function execute(): void
    {
        parent::execute();

        // action to execute
        $action = $this->getRequest()->query->get('action');
        if (!in_array($action, ['delete'])) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=no-action-selected');
        }

        // form id
        $formId = $this->getRequest()->query->getInt('form_id');

        // no id's provided
        if (!$this->getRequest()->query->has('id')) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=no-items-selected');
        } elseif ($action == '') {
            // no action provided
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=no-action-selected');
        } elseif (!BackendFormBuilderModel::exists($formId)) {
            // valid form id
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        } else {
            // redefine id's
            $ids = (array) $this->getRequest()->query->get('id');

            // delete comment(s)
            if ($action == 'delete') {
                BackendFormBuilderModel::deleteData($ids);
            }

            // define report
            $report = (count($ids) > 1) ? 'items-' : 'item-';

            // init var
            if ($action === 'delete') {
                $report .= 'deleted';
            }

            // redirect
            $this->redirect(BackendModel::createUrlForAction('Data') . '&id=' . $formId . '&report=' . $report);
        }
    }
}
