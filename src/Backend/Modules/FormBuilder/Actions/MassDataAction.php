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
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class MassDataAction extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // action to execute
        $action = \SpoonFilter::getGetValue('action', array('delete'), '');

        // form id
        $formId = \SpoonFilter::getGetValue('form_id', null, '', 'int');

        // no id's provided
        if (!isset($_GET['id'])) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=no-items-selected');
        } elseif ($action == '') {
            // no action provided
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=no-action-selected');
        } elseif (!BackendFormBuilderModel::exists($formId)) {
            // valid form id
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        } else {
            // redefine id's
            $ids = (array) $_GET['id'];

            // delete comment(s)
            if ($action == 'delete') {
                BackendFormBuilderModel::deleteData($ids);
            }

            // define report
            $report = (count($ids) > 1) ? 'items-' : 'item-';

            // init var
            if ($action == 'delete') {
                $report .= 'deleted';
            }

            // redirect
            $this->redirect(BackendModel::createURLForAction('Data') . '&id=' . $formId . '&report=' . $report);
        }
    }
}
