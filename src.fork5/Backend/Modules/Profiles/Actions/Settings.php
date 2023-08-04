<?php

namespace Backend\Modules\Profiles\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Frontend\Modules\Profiles\Engine\Model;

/**
 * This is the settings-action, it will display a form to set general profiles settings
 */
class Settings extends BackendBaseActionEdit
{
    public function execute(): void
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        // init settings form
        $this->form = new BackendForm('settings');

        $this->form->addCheckbox(
            'limit_display_name_changes',
            $this->get('fork.settings')->get(
                $this->url->getModule(),
                'limit_display_name_changes',
                false
            )
        );

        $this->form->addText(
            'max_display_name_changes',
            $this->get('fork.settings')->get(
                $this->url->getModule(),
                'max_display_name_changes',
                Model::MAX_DISPLAY_NAME_CHANGES
            )
        )->setAttribute('type', 'number');

        // send email for new profile to admin
        $this->form->addCheckbox(
            'send_new_profile_admin_mail',
            $this->get('fork.settings')->get(
                $this->url->getModule(),
                'send_new_profile_admin_mail',
                false
            )
        );

        $this->form->addCheckbox(
            'overwrite_profile_notification_email',
            (bool) ($this->get('fork.settings')->get(
                $this->url->getModule(),
                'profile_notification_email',
                null
            ) !== null)
        );

        $this->form->addText(
            'profile_notification_email',
            $this->get('fork.settings')->get(
                $this->url->getModule(),
                'profile_notification_email',
                null
            )
        );

        // send email for new profile to profile
        $this->form->addCheckbox(
            'send_new_profile_mail',
            $this->get('fork.settings')->get(
                $this->url->getModule(),
                'send_new_profile_mail',
                false
            )
        );
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            if ($this->form->getField('send_new_profile_admin_mail')->isChecked()) {
                if ($this->form->getField('overwrite_profile_notification_email')->isChecked()) {
                    $this->form->getField('profile_notification_email')->isEmail(BL::msg('EmailIsRequired'));
                }
            }

            if ($this->form->getField('limit_display_name_changes')->isChecked()) {
                $maxDisplayNameChanges = intval($this->form->getField('max_display_name_changes')->getValue());

                if ($maxDisplayNameChanges <= 0) {
                    $this->form->getField('max_display_name_changes')->addError(
                        BL::getError('InvalidNumberOfChanges')
                    );
                }
            }

            if ($this->form->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'send_new_profile_admin_mail',
                    (bool) $this->form->getField('send_new_profile_admin_mail')->getValue()
                );

                $profileNotificationEmail = null;

                if ($this->form->getField('overwrite_profile_notification_email')->isChecked()) {
                    $profileNotificationEmail = $this->form->getField('profile_notification_email')->getValue();
                }

                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'profile_notification_email',
                    $profileNotificationEmail
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'send_new_profile_mail',
                    (bool) $this->form->getField('send_new_profile_mail')->getValue()
                );

                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'limit_display_name_changes',
                    $this->form->getField('limit_display_name_changes')->isChecked()
                );

                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'max_display_name_changes',
                    intval($this->form->getField('max_display_name_changes')->getValue())
                );

                // redirect to the settings page
                $this->redirect(BackendModel::createUrlForAction('Settings') . '&report=saved-settings');
            }
        }
    }
}
