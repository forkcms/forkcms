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
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
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
     * @var BackendDataGridDB
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

    /**
     * Execute the action.
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist?
        if ($this->id !== null && BackendProfilesModel::exists($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadGroups();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the profile data.
     */
    private function getData()
    {
        // get general info
        $this->profile = BackendProfilesModel::get($this->id);

        $this->notifyProfile = $this->get('fork.settings')->get(
            $this->URL->getModule(),
            'send_new_profile_mail',
            false
        );
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // gender dropdown values
        $genderValues = array(
            'male' => \SpoonFilter::ucfirst(BL::getLabel('Male')),
            'female' => \SpoonFilter::ucfirst(BL::getLabel('Female')),
        );

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
        $this->frm = new BackendForm('edit');

        // create elements
        $this->frm->addCheckbox('new_email');
        $this->frm->addText('email', $this->profile['email']);
        $this->frm->addCheckbox('new_password');
        $this->frm->addPassword('password');
        $this->frm->addPassword('password_repeat');
        $this->frm->addText('display_name', $this->profile['display_name']);
        $this->frm->addText('first_name', BackendProfilesModel::getSetting($this->id, 'first_name'));
        $this->frm->addText('last_name', BackendProfilesModel::getSetting($this->id, 'last_name'));
        $this->frm->addText('city', BackendProfilesModel::getSetting($this->id, 'city'));
        $this->frm->addDropdown('gender', $genderValues, BackendProfilesModel::getSetting($this->id, 'gender'));
        $this->frm->addDropdown('day', array_combine($days, $days), $birthDay);
        $this->frm->addDropdown('month', $months, $birthMonth);
        $this->frm->addDropdown('year', array_combine($years, $years), (int) $birthYear);
        $this->frm->addDropdown(
            'country',
            Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()),
            BackendProfilesModel::getSetting($this->id, 'country')
        );

        // set default elements dropdowns
        $this->frm->getField('gender')->setDefaultElement('');
        $this->frm->getField('day')->setDefaultElement('');
        $this->frm->getField('month')->setDefaultElement('');
        $this->frm->getField('year')->setDefaultElement('');
        $this->frm->getField('country')->setDefaultElement('');
    }

    /**
     * Load the data grid with groups.
     */
    private function loadGroups()
    {
        // create the data grid
        $this->dgGroups = new BackendDataGridDB(
            BackendProfilesModel::QRY_DATAGRID_BROWSE_PROFILE_GROUPS,
            array($this->profile['id'])
        );

        // sorting columns
        $this->dgGroups->setSortingColumns(array('group_name'), 'group_name');

        // disable paging
        $this->dgGroups->setPaging(false);

        // set column function
        $this->dgGroups->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getLongDate'),
            array('[expires_on]'),
            'expires_on',
            true
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditProfileGroup')) {
            // set column URLs
            $this->dgGroups->setColumnURL(
                'group_name',
                BackendModel::createURLForAction('EditProfileGroup') . '&amp;id=[id]&amp;profile_id=' . $this->id
            );

            // edit column
            $this->dgGroups->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createURLForAction('EditProfileGroup') . '&amp;id=[id]&amp;profile_id=' . $this->id,
                BL::getLabel('Edit')
            );
        }
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('notifyProfile', $this->notifyProfile);

        // assign the active record and additional variables
        $this->tpl->assign('profile', $this->profile);

        // parse data grids
        $this->tpl->assign('dgGroups', ($this->dgGroups->getNumResults() != 0) ? $this->dgGroups->getContent() : false);

        // show delete or undelete button?
        if ($this->profile['status'] === 'deleted') {
            $this->tpl->assign('deleted', true);
        }

        // show block or unblock button?
        if ($this->profile['status'] === 'blocked') {
            $this->tpl->assign('blocked', true);
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // get fields
            $chkNewEmail = $this->frm->getField('new_email');
            $txtEmail = $this->frm->getField('email');
            $txtDisplayName = $this->frm->getField('display_name');
            $chkNewPassword = $this->frm->getField('new_password');
            $txtPassword = $this->frm->getField('password');
            $txtPasswordRepeat = $this->frm->getField('password_repeat');
            $txtFirstName = $this->frm->getField('first_name');
            $txtLastName = $this->frm->getField('last_name');
            $txtCity = $this->frm->getField('city');
            $ddmGender = $this->frm->getField('gender');
            $ddmDay = $this->frm->getField('day');
            $ddmMonth = $this->frm->getField('month');
            $ddmYear = $this->frm->getField('year');
            $ddmCountry = $this->frm->getField('country');

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
            if ($this->frm->isCorrect()) {
                // build item
                $values['email'] = ($chkNewEmail->isChecked()) ?
                    $txtEmail->getValue() : $this->profile['email'];

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
                    // get new salt
                    $salt = BackendProfilesModel::getRandomString();

                    // update salt
                    BackendProfilesModel::setSetting($this->id, 'salt', $salt);

                    // new password filled in? otherwise generate a password
                    $password = ($txtPassword->isFilled()) ?
                        $txtPassword->getValue() : BackendModel::generatePassword(8);

                    // build password
                    $values['password'] = BackendProfilesModel::getEncryptedString(
                        $password,
                        $salt
                    );
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

                $redirectUrl = BackendModel::createURLForAction('Index') .
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
                        array(
                            'id' => $this->id,
                            'first_name' => $txtFirstName->getValue(),
                            'last_name' => $txtLastName->getValue(),
                            'unencrypted_password' => $password,
                        )
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
}
