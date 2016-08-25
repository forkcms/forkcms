<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the settings-action, it will display a form to set general profiles settings
 */
class Settings extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Loads the settings form
     */
    private function loadForm()
    {
        // init settings form
        $this->frm = new BackendForm('settings');

        // send email for new profile to admin
        $this->frm->addCheckbox(
            'send_new_profile_admin_mail',
            $this->get('fork.settings')->get(
                $this->URL->getModule(),
                'send_new_profile_admin_mail',
                false
            )
        );

        $this->frm->addCheckbox(
            'overwrite_profile_notification_email',
            (bool) ($this->get('fork.settings')->get(
                $this->URL->getModule(),
                'profile_notification_email',
                null
            ) !== null)
        );

        $this->frm->addText(
            'profile_notification_email',
            $this->get('fork.settings')->get(
                $this->URL->getModule(),
                'profile_notification_email',
                null
            )
        );

        // send email for new profile to profile
        $this->frm->addCheckbox(
            'send_new_profile_mail',
            $this->get('fork.settings')->get(
                $this->URL->getModule(),
                'send_new_profile_mail',
                false
            )
        );
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            if ($this->frm->getField('send_new_profile_admin_mail')->isChecked()) {
                if ($this->frm->getField('overwrite_profile_notification_email')->isChecked()) {
                    $this->frm->getField('profile_notification_email')->isEmail(BL::msg('EmailIsRequired'));
                }
            }

            if ($this->frm->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set(
                    $this->URL->getModule(),
                    'send_new_profile_admin_mail',
                    (bool) $this->frm->getField('send_new_profile_admin_mail')->getValue()
                );

                $profileNotificationEmail = null;

                if ($this->frm->getField('overwrite_profile_notification_email')->isChecked()) {
                    $profileNotificationEmail = $this->frm->getField('profile_notification_email')->getValue();
                }

                $this->get('fork.settings')->set(
                    $this->URL->getModule(),
                    'profile_notification_email',
                    $profileNotificationEmail
                );
                $this->get('fork.settings')->set(
                    $this->URL->getModule(),
                    'send_new_profile_mail',
                    (bool) $this->frm->getField('send_new_profile_mail')->getValue()
                );

                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved-settings');
            }
        }
    }
}
