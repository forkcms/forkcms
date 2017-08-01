<?php

namespace Backend\Modules\Users\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the add-action, it will display a form to create a new user
 */
class Add extends BackendBaseActionAdd
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
        // create form
        $this->form = new BackendForm('add');

        // get the groups
        $groups = BackendGroupsModel::getAll();

        // if there is only one group we can check it so the user isn't bothered with an error for not selecting one
        $checkedGroups = (count($groups) == 1) ? $groups[0]['value'] : null;

        // create elements
        // profile
        $this->form
            ->addText('email', null, 255)
            ->setAttribute('type', 'email')
        ;
        $this->form->addPassword(
            'password',
            null,
            75,
            'form-control passwordGenerator',
            'form-control danger passwordGenerator'
        )->setAttributes(['autocomplete' => 'off']);
        $this->form->addPassword('confirm_password', null, 75)->setAttributes(['autocomplete' => 'off']);
        $this->form->addText('name', null, 255);
        $this->form->addText('surname', null, 255);
        $this->form->addText('nickname', null, 24);
        $this->form->addImage('avatar');

        $this->form->addDropdown(
            'interface_language',
            BL::getInterfaceLanguages(),
            $this->get('fork.settings')->get('Core', 'default_interface_language')
        );
        $this->form->addDropdown(
            'date_format',
            BackendUsersModel::getDateFormats(),
            BackendAuthentication::getUser()->getSetting('date_format')
        );
        $this->form->addDropdown(
            'time_format',
            BackendUsersModel::getTimeFormats(),
            BackendAuthentication::getUser()->getSetting('time_format')
        );
        $this->form->addDropdown(
            'number_format',
            BackendUsersModel::getNumberFormats(),
            BackendAuthentication::getUser()->getSetting('number_format', 'dot_nothing')
        );

        $this->form->addDropdown('csv_split_character', BackendUsersModel::getCSVSplitCharacters());
        $this->form->addDropdown('csv_line_ending', BackendUsersModel::getCSVLineEndings());

        // permissions
        $this->form->addCheckbox('active', true);
        $this->form->addMultiCheckbox('groups', $groups, $checkedGroups);
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // email is present
            if ($this->form->getField('email')->isFilled(BL::err('EmailIsRequired'))) {
                // is this an email-address
                if ($this->form->getField('email')->isEmail(BL::err('EmailIsInvalid'))) {
                    // was this emailaddress deleted before
                    if (BackendUsersModel::emailDeletedBefore(
                        $this->form->getField('email')->getValue()
                    )
                    ) {
                        $this->form->getField('email')->addError(
                            sprintf(
                                BL::err('EmailWasDeletedBefore'),
                                BackendModel::createUrlForAction(
                                    'UndoDelete',
                                    null,
                                    null,
                                    ['email' => $this->form->getField('email')->getValue()]
                                )
                            )
                        );
                    } else {
                        // email already exists
                        if (BackendUsersModel::existsEmail(
                            $this->form->getField('email')->getValue()
                        )
                        ) {
                            $this->form->getField('email')->addError(BL::err('EmailAlreadyExists'));
                        }
                    }
                }
            }

            // required fields
            $this->form->getField('password')->isFilled(BL::err('PasswordIsRequired'));
            $this->form->getField('nickname')->isFilled(BL::err('NicknameIsRequired'));
            $this->form->getField('name')->isFilled(BL::err('NameIsRequired'));
            $this->form->getField('surname')->isFilled(BL::err('SurnameIsRequired'));
            $this->form->getField('interface_language')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('date_format')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('time_format')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('number_format')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('groups')->isFilled(BL::err('FieldIsRequired'));
            if ($this->form->getField('password')->isFilled()) {
                if ($this->form->getField('password')->getValue() !== $this->form->getField('confirm_password')->getValue()) {
                    $this->form->getField('confirm_password')->addError(BL::err('ValuesDontMatch'));
                }
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // build settings-array
                $settings = [];
                $settings['nickname'] = $this->form->getField('nickname')->getValue();
                $settings['name'] = $this->form->getField('name')->getValue();
                $settings['surname'] = $this->form->getField('surname')->getValue();
                $settings['interface_language'] = $this->form->getField('interface_language')->getValue();
                $settings['date_format'] = $this->form->getField('date_format')->getValue();
                $settings['time_format'] = $this->form->getField('time_format')->getValue();
                $settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
                $settings['number_format'] = $this->form->getField('number_format')->getValue();
                $settings['csv_split_character'] = $this->form->getField('csv_split_character')->getValue();
                $settings['csv_line_ending'] = $this->form->getField('csv_line_ending')->getValue();
                $settings['current_password_change'] = time();
                $settings['avatar'] = 'no-avatar.gif';

                // get selected groups
                $groups = $this->form->getField('groups')->getChecked();

                // init var
                $newSequence = BackendGroupsModel::getSetting($groups[0], 'dashboard_sequence');

                $sequences = [];
                // loop through groups and collect all dashboard widget sequences
                foreach ($groups as $group) {
                    $sequences[] = BackendGroupsModel::getSetting($group, 'dashboard_sequence');
                }

                // loop through sequences
                foreach ($sequences as $sequence) {
                    // loop through modules inside a sequence
                    foreach ($sequence as $moduleKey => $module) {
                        // loop through widgets inside a module
                        foreach ($module as $widgetKey => $widget) {
                            // if widget present set true
                            $newSequence[$moduleKey][] = $widgetKey;
                        }
                    }
                }

                // add new sequence to settings
                $settings['dashboard_sequence'] = $newSequence;

                // build user-array
                $user = [];
                $user['email'] = $this->form->getField('email')->getValue();
                $user['password'] = BackendAuthentication::encryptPassword(
                    $this->form->getField('password')->getValue(true)
                );

                // save the password strength
                $passwordStrength = BackendAuthentication::checkPassword(
                    $this->form->getField('password')->getValue(true)
                );
                $settings['password_strength'] = $passwordStrength;

                // save changes
                $user['id'] = (int) BackendUsersModel::insert($user, $settings);

                // has the user submitted an avatar?
                if ($this->form->getField('avatar')->isFilled()) {
                    // create new filename
                    $filename = mt_rand(0, 3) . '_' . $user['id'] . '.' . $this->form->getField('avatar')->getExtension();

                    // add into settings to update
                    $settings['avatar'] = $filename;

                    // resize (128x128)
                    $this->form->getField('avatar')->createThumbnail(
                        FRONTEND_FILES_PATH . '/Users/avatars/128x128/' . $filename,
                        128,
                        128,
                        true,
                        false,
                        100
                    );

                    // resize (64x64)
                    $this->form->getField('avatar')->createThumbnail(
                        FRONTEND_FILES_PATH . '/Users/avatars/64x64/' . $filename,
                        64,
                        64,
                        true,
                        false,
                        100
                    );

                    // resize (32x32)
                    $this->form->getField('avatar')->createThumbnail(
                        FRONTEND_FILES_PATH . '/Users/avatars/32x32/' . $filename,
                        32,
                        32,
                        true,
                        false,
                        100
                    );
                }

                // update settings (in this case the avatar)
                BackendUsersModel::update($user, $settings);

                // save groups
                BackendGroupsModel::insertMultipleGroups($user['id'], $groups);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction(
                        'Index'
                    ) . '&report=added&var=' . $settings['nickname'] . '&highlight=row-' . $user['id']
                );
            }
        }
    }
}
