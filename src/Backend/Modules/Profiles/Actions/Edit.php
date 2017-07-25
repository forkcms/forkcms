<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use Symfony\Component\Intl\Intl as Intl;

/**
 * This is the edit-action, it will display a form to edit an existing profile.
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * Groups data grid.
     *
     * @var BackendDataGridDatabase
     */
    private $dgGroups;

    /**
     * @var bool
     */
    private $notifyProfile;

    /**
     * Info about the current profile.
     *
     * @var array
     */
    private $profile;

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist?
        if ($this->id !== 0 && BackendProfilesModel::exists($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadGroups();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        // get general info
        $this->profile = BackendProfilesModel::get($this->id);

        $this->notifyProfile = $this->get('fork.settings')->get(
            $this->url->getModule(),
            'send_new_profile_mail',
            false
        );
    }

    private function loadForm(): void
    {
        // gender dropdown values
        $genderValues = [
            'male' => \SpoonFilter::ucfirst(BL::getLabel('Male')),
            'female' => \SpoonFilter::ucfirst(BL::getLabel('Female')),
        ];

        // birthdate dropdown values
        $days = range(1, 31);
        $months = \SpoonLocale::getMonths(BL::getInterfaceLanguage());
        $years = range(date('Y'), 1900);

        // get settings
        $birthDate = BackendProfilesModel::getSetting($this->id, 'birth_date');

        // get day, month and year
        if ($birthDate) {
            list($birthYear, $birthMonth, $birthDay) = explode('-', $birthDate);
        } else {
            // no birth date setting
            $birthDay = '';
            $birthMonth = '';
            $birthYear = '';
        }

        // create form
        $this->form = new BackendForm('edit');

        // create elements
        $this->form->addCheckbox('new_email');
        $this->form->addText('email', $this->profile['email']);
        $this->form->addCheckbox('new_password');
        $this->form->addPassword('password');
        $this->form->addPassword('password_repeat');
        $this->form->addText('display_name', $this->profile['display_name']);
        $this->form->addText('first_name', BackendProfilesModel::getSetting($this->id, 'first_name'));
        $this->form->addText('last_name', BackendProfilesModel::getSetting($this->id, 'last_name'));
        $this->form->addText('city', BackendProfilesModel::getSetting($this->id, 'city'));
        $this->form->addDropdown('gender', $genderValues, BackendProfilesModel::getSetting($this->id, 'gender'));
        $this->form->addDropdown('day', array_combine($days, $days), $birthDay);
        $this->form->addDropdown('month', $months, $birthMonth);
        $this->form->addDropdown('year', array_combine($years, $years), (int) $birthYear);
        $this->form->addDropdown(
            'country',
            Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()),
            BackendProfilesModel::getSetting($this->id, 'country')
        );

        // set default elements dropdowns
        $this->form->getField('gender')->setDefaultElement('');
        $this->form->getField('day')->setDefaultElement('');
        $this->form->getField('month')->setDefaultElement('');
        $this->form->getField('year')->setDefaultElement('');
        $this->form->getField('country')->setDefaultElement('');
    }

    private function loadGroups(): void
    {
        // create the data grid
        $this->dgGroups = new BackendDataGridDatabase(
            BackendProfilesModel::QUERY_DATAGRID_BROWSE_PROFILE_GROUPS,
            [$this->profile['id']]
        );

        // sorting columns
        $this->dgGroups->setSortingColumns(['group_name'], 'group_name');

        // disable paging
        $this->dgGroups->setPaging(false);

        // set column function
        $this->dgGroups->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[expires_on]'],
            'expires_on',
            true
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditProfileGroup')) {
            // set column URLs
            $this->dgGroups->setColumnURL(
                'group_name',
                BackendModel::createUrlForAction('EditProfileGroup') . '&amp;id=[id]&amp;profile_id=' . $this->id
            );

            // edit column
            $this->dgGroups->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createUrlForAction('EditProfileGroup') . '&amp;id=[id]&amp;profile_id=' . $this->id,
                BL::getLabel('Edit')
            );
        }
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('notifyProfile', $this->notifyProfile);

        // assign the active record and additional variables
        $this->template->assign('profile', $this->profile);

        // parse data grids
        $this->template->assign('dgGroups', ($this->dgGroups->getNumResults() != 0) ? $this->dgGroups->getContent() : false);

        // show delete or undelete button?
        if ($this->profile['status'] === 'deleted') {
            $this->template->assign('deleted', true);
        }

        // show block or unblock button?
        if ($this->profile['status'] === 'blocked') {
            $this->template->assign('blocked', true);
        }
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // get fields
            $chkNewEmail = $this->form->getField('new_email');
            $txtEmail = $this->form->getField('email');
            $txtDisplayName = $this->form->getField('display_name');
            $chkNewPassword = $this->form->getField('new_password');
            $txtPassword = $this->form->getField('password');
            $txtPasswordRepeat = $this->form->getField('password_repeat');
            $txtFirstName = $this->form->getField('first_name');
            $txtLastName = $this->form->getField('last_name');
            $txtCity = $this->form->getField('city');
            $ddmGender = $this->form->getField('gender');
            $ddmDay = $this->form->getField('day');
            $ddmMonth = $this->form->getField('month');
            $ddmYear = $this->form->getField('year');
            $ddmCountry = $this->form->getField('country');

            // email filled in?
            if ($chkNewEmail->isChecked() && $txtEmail->isFilled(BL::getError('EmailIsRequired'))) {
                // email must not be the same as previous one
                if ($txtEmail->getValue() == $this->profile['email']) {
                    $txtEmail->addError(BL::getError('EmailMatchesPrevious'));
                }

                // valid email?
                if ($txtEmail->isEmail(BL::getError('EmailIsInvalid'))) {
                    // email already exists?
                    if (BackendProfilesModel::existsByEmail($txtEmail->getValue(), $this->id)) {
                        // set error
                        $txtEmail->addError(BL::getError('EmailExists'));
                    }
                }
            }

            // display name filled in?
            if ($txtDisplayName->isFilled(BL::getError('DisplayNameIsRequired'))) {
                // display name already exists?
                if (BackendProfilesModel::existsDisplayName($txtDisplayName->getValue(), $this->id)) {
                    // set error
                    $txtDisplayName->addError(BL::getError('DisplayNameExists'));
                }
            }

            // new_password is checked, so verify new password (only if profile should not be notified)
            // because then if the password field is empty, it will generate a new password
            if ($chkNewPassword->isChecked() && !$this->notifyProfile) {
                $txtPassword->isFilled(BL::err('FieldIsRequired'));
                $txtPasswordRepeat->isFilled(BL::err('FieldIsRequired'));

                // both password fields are filled in and should match
                if ($txtPassword->isFilled() && $txtPasswordRepeat->isFilled()
                    && ($txtPassword->getValue() != $txtPasswordRepeat->getValue())) {
                    $txtPasswordRepeat->addError(BL::err('PasswordRepeatIsRequired'));
                }
            }

            // one of the bday fields are filled in
            if ($ddmDay->isFilled() || $ddmMonth->isFilled() || $ddmYear->isFilled()) {
                // valid date?
                if (!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue())) {
                    // set error
                    $ddmYear->addError(BL::getError('DateIsInvalid'));
                }
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // build item
                $values = ['email' => $chkNewEmail->isChecked() ? $txtEmail->getValue() : $this->profile['email']];

                // only update if display name changed
                if ($txtDisplayName->getValue() != $this->profile['display_name']) {
                    $values['display_name'] = $txtDisplayName->getValue();
                    $values['url'] = BackendProfilesModel::getUrl(
                        $txtDisplayName->getValue(),
                        $this->id
                    );
                }

                // new password filled in?
                if ($chkNewPassword->isChecked()) {
                    // new password filled in? otherwise generate a password
                    $password = ($txtPassword->isFilled()) ?
                        $txtPassword->getValue() : BackendModel::generatePassword(8);

                    // build password
                    $values['password'] = BackendProfilesModel::encryptPassword($password);
                }

                // update values
                BackendProfilesModel::update($this->id, $values);

                // birthday is filled in
                if ($ddmYear->isFilled()) {
                    // mysql format
                    $birthDate = $ddmYear->getValue() . '-';
                    $birthDate .= str_pad($ddmMonth->getValue(), 2, '0', STR_PAD_LEFT) . '-';
                    $birthDate .= str_pad($ddmDay->getValue(), 2, '0', STR_PAD_LEFT);
                } else {
                    $birthDate = null;
                }

                // update settings
                BackendProfilesModel::setSetting($this->id, 'first_name', $txtFirstName->getValue());
                BackendProfilesModel::setSetting($this->id, 'last_name', $txtLastName->getValue());
                BackendProfilesModel::setSetting($this->id, 'gender', $ddmGender->getValue());
                BackendProfilesModel::setSetting($this->id, 'birth_date', $birthDate);
                BackendProfilesModel::setSetting($this->id, 'city', $txtCity->getValue());
                BackendProfilesModel::setSetting($this->id, 'country', $ddmCountry->getValue());

                $displayName = (isset($values['display_name'])) ?
                    $values['display_name'] : $this->profile['display_name'];

                $redirectUrl = BackendModel::createUrlForAction('Index') .
                               '&var=' . rawurlencode($values['email']) .
                    '&highlight=row-' . $this->id .
                    '&var=' . rawurlencode($displayName) .
                    '&report='
                ;

                if ($this->notifyProfile &&
                    ($chkNewEmail->isChecked() || $chkNewPassword->isChecked())
                ) {
                    // no new password
                    if (!$chkNewPassword->isChecked()) {
                        $password = BL::lbl('YourExistingPassword');
                    }

                    // notify values
                    $notifyValues = array_merge(
                        $values,
                        [
                            'id' => $this->id,
                            'first_name' => $txtFirstName->getValue(),
                            'last_name' => $txtLastName->getValue(),
                            'unencrypted_password' => $password,
                        ]
                    );

                    if (!isset($notifyValues['display_name'])) {
                        $notifyValues['display_name'] = $this->profile['display_name'];
                    }

                    BackendProfilesModel::notifyProfile($notifyValues, true);

                    $redirectUrl .= 'saved-and-notified';
                } else {
                    $redirectUrl .= 'saved';
                }

                // everything is saved, so redirect to the overview
                $this->redirect($redirectUrl);
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->profile['id']],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
