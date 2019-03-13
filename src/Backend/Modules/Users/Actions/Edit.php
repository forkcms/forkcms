<?php

namespace Backend\Modules\Users\Actions;

use Backend\Form\Type\DeleteType;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\User as BackendUser;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the edit-action, it will display a form to alter the user-details and settings
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * The authenticated user
     *
     * @var BackendUser
     */
    private $authenticatedUser;

    /**
     * Can only edit his own profile
     *
     * @var bool
     */
    private $allowUserRights;

    /**
     * The user
     *
     * @var BackendUser
     */
    private $user;

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');
        $error = $this->getRequest()->query->get('error', '');
        $this->loadAuthenticatedUser();

        // If id and error parameters are not set we'll assume the user logged in
        // and has been redirected to this action by the authentication index action.
        // When this is the case the user will be redirected to the index action of this module.
        // An action to which he may not have any user rights.
        // Redirect to the user's own profile instead to avoid unnessary words.
        if ($this->id === 0
            && $error === ''
            && $this->authenticatedUser->getUserId()
        ) {
            $this->redirect(
                BackendModel::createUrlForAction(
                    'Edit'
                ) . '&id=' . $this->authenticatedUser->getUserId()
            );
        }

        // does the user exists
        if ($this->id !== null && BackendUsersModel::exists($this->id)) {
            parent::execute();
            $this->record = (array) BackendUsersModel::get($this->id);
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Load the authenticated user in a separate method so we can load it before the form starts loading.
     */
    private function loadAuthenticatedUser(): void
    {
        $this->authenticatedUser = BackendAuthentication::getUser();
    }

    private function loadForm(): void
    {
        // create user objects
        $this->user = new BackendUser($this->id);
        $this->allowUserRights = (
            (BackendAuthentication::isAllowedAction('Add') || $this->authenticatedUser->getUserId() != $this->id) ||
            $this->authenticatedUser->isGod()
        );

        // redirect to error page when not allowed to edit other profiles
        if (!$this->authenticatedUser->isGod()
            && ($this->authenticatedUser->getUserId() != $this->id && !BackendAuthentication::isAllowedAction('Add'))
        ) {
            $this->redirect(BackendModel::createUrlForAction('Error') . '&type=not-allowed');
        }

        // create form
        $this->form = new BackendForm('edit');

        // get active groups
        $groups = BackendGroupsModel::getGroupsByUser($this->id);

        $checkedGroups = [];
        // loop through groups and set checked
        foreach ($groups as $group) {
            $checkedGroups[] = $group['id'];
        }

        // create elements
        // profile
        $this->form
            ->addText('email', $this->record['email'], 255)
            ->setAttribute('type', 'email')
        ;
        if ($this->user->isGod()) {
            $this->form->getField('email')->setAttributes(['disabled' => 'disabled']);
        }
        $this->form->addText('name', $this->record['settings']['name'], 255);
        $this->form->addText('surname', $this->record['settings']['surname'], 255);
        $this->form->addText('nickname', $this->record['settings']['nickname'], 24);
        $this->form->addImage('avatar');

        // password
        // check if we're god or same user
        if ($this->authenticatedUser->getUserId() == $this->id || $this->authenticatedUser->isGod()) {
            // allow to set new password
            $this->form->addPassword('new_password', null, 75);
            $this->form->addPassword('confirm_password', null, 75);

            // disable autocomplete
            $this->form->getField('new_password')->setAttributes(['autocomplete' => 'off']);
            $this->form->getField('confirm_password')->setAttributes(['autocomplete' => 'off']);
        }

        // settings
        $this->form->addDropdown(
            'interface_language',
            BL::getInterfaceLanguages(),
            $this->record['settings']['interface_language']
        );
        $this->form->addDropdown(
            'preferred_editor',
            BackendModel::getContainer()->getParameter('fork.form.available_editors'),
            $this->record['settings']['preferred_editor'] ?? BackendModel::getPreferredEditor()
        );
        $this->form->addDropdown(
            'date_format',
            BackendUsersModel::getDateFormats(),
            $this->user->getSetting('date_format')
        );
        $this->form->addDropdown(
            'time_format',
            BackendUsersModel::getTimeFormats(),
            $this->user->getSetting('time_format')
        );
        $this->form->addDropdown(
            'number_format',
            BackendUsersModel::getNumberFormats(),
            $this->user->getSetting('number_format', 'dot_nothing')
        );

        $this->form->addDropdown(
            'csv_split_character',
            BackendUsersModel::getCSVSplitCharacters(),
            $this->user->getSetting('csv_split_character')
        );
        $this->form->addDropdown(
            'csv_line_ending',
            BackendUsersModel::getCSVLineEndings(),
            $this->user->getSetting('csv_line_ending')
        );

        // permissions
        $this->form->addCheckbox('active', $this->record['active']);

        // only when GOD or when you can edit other users
        if ($this->allowUserRights) {
            // disable active field for current users
            if ($this->authenticatedUser->getUserId() == $this->record['id']) {
                $this->form->getField(
                    'active'
                )->setAttribute('disabled', 'disabled');
            }
            $this->form->addMultiCheckbox('groups', BackendGroupsModel::getAll(), $checkedGroups);
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // reset avatar URL
        if ($this->record['settings']['avatar'] != '') {
            $this->record['settings']['avatar'] .= '?time=' . time();
        }

        // only allow deletion of other users
        $this->template->assign(
            'allowUsersDelete',
            $this->authenticatedUser->getUserId() != $this->id
        );

        // assign
        $this->template->assign('record', $this->record);
        $this->template->assign('id', $this->id);

        // assign that we're god or the same user
        $this->template->assign(
            'allowPasswordEdit',
            ($this->authenticatedUser->getUserId() == $this->id || $this->authenticatedUser->isGod())
        );

        // assign that you can edit the user rights
        $this->template->assign('allowUserRights', $this->allowUserRights);

        // check if we need to show the password strength and parse the label
        $this->template->assign('showPasswordStrength', ($this->record['settings']['password_strength'] !== 'strong'));
        $this->template->assign('passwordStrengthLabel', BL::lbl($this->record['settings']['password_strength']));

        $this->header->appendDetailToBreadcrumbs($this->user->getEmail());
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();
            $fields = $this->form->getFields();

            // email is present
            if (!$this->user->isGod()) {
                if ($fields['email']->isFilled(BL::err('EmailIsRequired'))) {
                    // is this an email-address
                    if ($fields['email']->isEmail(BL::err('EmailIsInvalid'))) {
                        // was this emailaddress deleted before
                        if (BackendUsersModel::emailDeletedBefore($fields['email']->getValue())) {
                            $fields['email']->addError(
                                sprintf(
                                    BL::err('EmailWasDeletedBefore'),
                                    BackendModel::createUrlForAction(
                                        'UndoDelete',
                                        null,
                                        null,
                                        ['email' => $fields['email']->getValue()]
                                    )
                                )
                            );
                        } elseif (BackendUsersModel::existsEmail($fields['email']->getValue(), $this->id)) {
                            // email already exists
                            $fields['email']->addError(BL::err('EmailAlreadyExists'));
                        }
                    }
                }
            }

            // required fields
            if ($this->user->isGod() && $fields['email']->getValue() != ''
                && $this->user->getEmail() != $fields['email']->getValue()
            ) {
                $fields['email']->addError(BL::err('CantChangeGodsEmail'));
            }
            if (!$this->user->isGod()) {
                $fields['email']->isEmail(BL::err('EmailIsInvalid'));
            }
            $fields['nickname']->isFilled(BL::err('NicknameIsRequired'));
            $fields['name']->isFilled(BL::err('NameIsRequired'));
            $fields['surname']->isFilled(BL::err('SurnameIsRequired'));
            $fields['interface_language']->isFilled(BL::err('FieldIsRequired'));
            $fields['preferred_editor']->isFilled(BL::err('FieldIsRequired'));
            $fields['date_format']->isFilled(BL::err('FieldIsRequired'));
            $fields['time_format']->isFilled(BL::err('FieldIsRequired'));
            $fields['number_format']->isFilled(BL::err('FieldIsRequired'));
            if ($this->allowUserRights) {
                $fields['groups']->isFilled(BL::err('FieldIsRequired'));
            }
            if (isset($fields['new_password']) && $fields['new_password']->isFilled()) {
                if ($fields['new_password']->getValue() !== $fields['confirm_password']->getValue()
                ) {
                    $fields['confirm_password']->addError(BL::err('ValuesDontMatch'));
                }
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // build user-array
                $user = ['id' => $this->id];
                if (!$this->user->isGod()) {
                    $user['email'] = $fields['email']->getValue(true);
                }
                if ($this->authenticatedUser->getUserId() != $this->record['id']
                ) {
                    $user['active'] = $fields['active']->isChecked();
                }

                // user is now de-activated, we now remove all sessions for this user so he is logged out immediately
                if (isset($user['active']) && !$user['active'] && $this->record['active'] !== $user['active']) {
                    // delete all sessions for user
                    BackendModel::get('database')->delete(
                        'users_sessions',
                        'user_id = ?',
                        [
                            $this->user->getUserId(),
                        ]
                    );
                }

                // build settings-array
                $settings = [];
                $settings['nickname'] = $fields['nickname']->getValue();
                $settings['name'] = $fields['name']->getValue();
                $settings['surname'] = $fields['surname']->getValue();
                $settings['interface_language'] = $fields['interface_language']->getValue();
                $settings['preferred_editor'] = $fields['preferred_editor']->getValue();
                $settings['date_format'] = $fields['date_format']->getValue();
                $settings['time_format'] = $fields['time_format']->getValue();
                $settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
                $settings['number_format'] = $fields['number_format']->getValue();
                $settings['csv_split_character'] = $fields['csv_split_character']->getValue();
                $settings['csv_line_ending'] = $fields['csv_line_ending']->getValue();

                // update password (only if filled in)
                if (isset($fields['new_password']) && $fields['new_password']->isFilled()) {
                    $user['password'] = BackendAuthentication::encryptPassword($fields['new_password']->getValue());

                    // the password has changed
                    if ($this->record['password'] != $user['password']) {
                        // save the login timestamp in the user's settings
                        $lastPasswordChange = BackendUsersModel::getSetting($user['id'], 'current_password_change');
                        $settings['current_password_change'] = time();
                        if ($lastPasswordChange) {
                            $settings['last_password_change'] = $lastPasswordChange;
                        }

                        // save the password strength
                        $passwordStrength = BackendAuthentication::checkPassword($fields['new_password']->getValue());
                        $settings['password_strength'] = $passwordStrength;
                    }
                }

                // has the user submitted an avatar?
                if ($fields['avatar']->isFilled()) {
                    $avatarsPath = FRONTEND_FILES_PATH . '/Users/avatars';

                    // delete old avatar if it isn't the default-image
                    if ($this->record['settings']['avatar'] != 'no-avatar.jpg'
                        && $this->record['settings']['avatar'] != ''
                    ) {
                        $filesystem = new Filesystem();
                        $filesystem->remove($avatarsPath . '/source/' . $this->record['settings']['avatar']);
                        $filesystem->remove($avatarsPath . '/128x128/' . $this->record['settings']['avatar']);
                        $filesystem->remove($avatarsPath . '/64x64/' . $this->record['settings']['avatar']);
                        $filesystem->remove($avatarsPath . '/32x32/' . $this->record['settings']['avatar']);
                    }

                    // create new filename
                    $filename = mt_rand(0, 3) . '_' . $user['id'] . '.' . $fields['avatar']->getExtension();

                    // add into settings to update
                    $settings['avatar'] = $filename;

                    // resize (128x128)
                    $fields['avatar']->createThumbnail(
                        $avatarsPath . '/128x128/' . $filename,
                        128,
                        128,
                        true,
                        false,
                        100
                    );

                    // resize (64x64)
                    $fields['avatar']->createThumbnail($avatarsPath . '/64x64/' . $filename, 64, 64, true, false, 100);

                    // resize (32x32)
                    $fields['avatar']->createThumbnail($avatarsPath . '/32x32/' . $filename, 32, 32, true, false, 100);
                }

                // save changes
                BackendUsersModel::update($user, $settings);

                // save groups
                if ($this->allowUserRights) {
                    BackendGroupsModel::insertMultipleGroups($this->id, $fields['groups']->getChecked());
                }

                // can only edit own profile
                if (!BackendAuthentication::isAllowedAction('Index')) {
                    // everything is saved, so redirect to the edit page
                    $this->redirect(
                        BackendModel::createUrlForAction(
                            'Edit'
                        ) . '&id=' . $this->id . '&report=edited&var=' . $settings['nickname']
                    );
                } else {
                    // everything is saved, so redirect to the overview
                    $this->redirect(
                        BackendModel::createUrlForAction(
                            'Index'
                        ) . '&report=edited&var=' . $settings['nickname'] . '&highlight=row-' . $user['id']
                    );
                }
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->user->getUserId()],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
