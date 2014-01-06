<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This action is used to update one or more e-mail addresses (delete, ...)
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class MassAddressAction extends BackendBaseAction
{
    /**
     * The passed e-mails
     *
     * @var    array
     */
    private $emails;

    /**
     * The group ID we have to perform the actions for
     *
     * @var    int
     */
    private $groupId;

    /**
     * Delete addresses
     */
    private function deleteAddresses()
    {
        // no group set
        if ($this->groupId == '') {
            $this->groupId = null;
        }

        // get all groups
        $groupIds = BackendMailmotorModel::getGroupIDs();

        // loop the emails
        foreach ($this->emails as $email) {
            // the group ID is not set
            if ($this->groupId == null) {
                // if no groups were set, break here
                if (empty($groupIds)) {
                    break;
                }

                // loop the group IDs
                foreach ($groupIds as $groupId) {
                    // try to unsubscribe this address
                    try {
                        BackendMailmotorCMHelper::unsubscribe($email, $groupId);
                    } catch (\Exception $e) {
                        // do nothing
                    }
                }

                // delete all addresses
                BackendMailmotorModel::deleteAddresses($email);
            } else {
                // group ID was set, unsubscribe the address for this group
                BackendMailmotorCMHelper::unsubscribe($email, $this->groupId);
            }
        }

        // trigger event
        BackendModel::triggerEvent($this->getModule(), 'after_delete_addresses');

        // redirect
        $this->redirect(
            BackendModel::createURLForAction(
                'Addresses'
            ) . '&report=delete-addresses' . (!empty($this->groupId) ? '&group_id=' . $this->groupId : '')
        );
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // action to execute
        $action = \SpoonFilter::getGetValue('action', array('delete', 'export'), '');
        $this->groupId = \SpoonFilter::getGetValue('group_id', null, '');

        // no id's provided
        if (!$action) {
            $this->redirect(BackendModel::createURLForAction('Addresses') . '&error=no-action-selected');
        }
        if (!isset($_GET['emails'])) {
            $this->redirect(
                BackendModel::createURLForAction('Addresses') . '&error=no-items-selected'
            );
        } else {
            // redefine id's
            $this->emails = (array) $_GET['emails'];

            // evaluate $action, see what action was triggered
            switch ($action) {
                case 'delete':
                    $this->deleteAddresses();
                    break;

                case 'export':
                    $this->exportAddresses();
                    break;
            }
        }
    }

    /**
     * Export addresses
     */
    private function exportAddresses()
    {
        // fetch the creationdate for the addresses
        foreach ($this->emails as &$email) {
            $address = BackendMailmotorModel::getAddress($email);
            $email = array(
                'email' => $email,
                'created_on' => strtotime($address['created_on'])
            );
        }

        // export the addresses
        BackendMailmotorModel::exportAddresses($this->emails);
    }
}
