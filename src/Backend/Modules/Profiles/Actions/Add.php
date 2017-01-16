<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use Symfony\Component\Intl\Intl as Intl;

/**
 * This is the add-action, it will display a form to add a new profile.
 */
class Add extends BackendBaseActionAdd
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $notifyAdmin;

    /**
     * @var bool
     */
    private $notifyProfile;

    /**
     * Execute the action.
     */
    public function execute()
    {
        parent::execute();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Get data
     */
    public function getData()
    {
        $this->notifyAdmin = $this->get('fork.settings')->get(
            $this->URL->getModule(),
            'send_new_profile_admin_mail',
            false
        );

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

        // create form
        $this->frm = new BackendForm('add');

        // create elements
        $this->frm
            ->addText('email')
            ->setAttribute('type', 'email')
        ;
        $this->frm->addPassword('password');
        $this->frm->addText('display_name');
        $this->frm->addText('first_name');
        $this->frm->addText('last_name');
        $this->frm->addText('city');
        $this->frm->addDropdown('gender', $genderValues);
        $this->frm->addDropdown('day', array_combine($days, $days));
        $this->frm->addDropdown('month', $months);
        $this->frm->addDropdown('year', array_combine($years, $years));
        $this->frm->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()));

        // set default elements dropdowns
        $this->frm->getField('gender')->setDefaultElement('');
        $this->frm->getField('day')->setDefaultElement('');
        $this->frm->getField('month')->setDefaultElement('');
        $this->frm->getField('year')->setDefaultElement('');
        $this->frm->getField('country')->setDefaultElement('');
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
            $txtEmail = $this->frm->getField('email');
            $txtDisplayName = $this->frm->getField('display_name');
            $txtPassword = $this->frm->getField('password');
            $txtFirstName = $this->frm->getField('first_name');
            $txtLastName = $this->frm->getField('last_name');
            $txtCity = $this->frm->getField('city');
            $ddmGender = $this->frm->getField('gender');
            $ddmDay = $this->frm->getField('day');
            $ddmMonth = $this->frm->getField('month');
            $ddmYear = $this->frm->getField('year');
            $ddmCountry = $this->frm->getField('country');

            // email filled in?
            if ($txtEmail->isFilled(BL::getError('EmailIsRequired'))) {
                // valid email?
                if ($txtEmail->isEmail(BL::getError('EmailIsInvalid'))) {
                    // email already exists?
                    if (BackendProfilesModel::existsByEmail($txtEmail->getValue())) {
                        // set error
                        $txtEmail->addError(BL::getError('EmailExists'));
                    }
                }
            }

            // display name filled in?
            if ($txtDisplayName->isFilled(BL::getError('DisplayNameIsRequired'))) {
                // display name already exists?
                if (BackendProfilesModel::existsDisplayName($txtDisplayName->getValue())) {
                    // set error
                    $txtDisplayName->addError(BL::getError('DisplayNameExists'));
                }
            }

            // profile must not be notified, password must not be empty
            if (!$this->notifyProfile) {
                $txtPassword->isFilled(BL::err('FieldIsRequired'));
            }

            // one of the birthday fields are filled in
            if ($ddmDay->isFilled() || $ddmMonth->isFilled() || $ddmYear->isFilled()) {
                // valid date?
                if (!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue())) {
                    // set error
                    $ddmYear->addError(BL::getError('DateIsInvalid'));
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                $salt = BackendProfilesModel::getRandomString();
                $password = ($txtPassword->isFilled()) ?
                    $txtPassword->getValue() : BackendModel::generatePassword(8);

                // build item
                $values = array(
                    'email' => $txtEmail->getValue(),
                    'registered_on' => BackendModel::getUTCDate(),
                    'display_name' => $txtDisplayName->getValue(),
                    'url' => BackendProfilesModel::getUrl($txtDisplayName->getValue()),
                    'last_login' => BackendModel::getUTCDate(null, 0),
                    'password' => BackendProfilesModel::getEncryptedString($password, $salt),
                );

                $this->id = BackendProfilesModel::insert($values);

                // update salt
                BackendProfilesModel::setSetting($this->id, 'salt', $salt);

                // bday is filled in
                if ($ddmYear->isFilled()) {
                    // mysql format
                    $birthDate = $ddmYear->getValue() . '-';
                    $birthDate .= str_pad($ddmMonth->getValue(), 2, '0', STR_PAD_LEFT) . '-';
                    $birthDate .= str_pad($ddmDay->getValue(), 2, '0', STR_PAD_LEFT);
                } else {
                    // not filled in
                    $birthDate = null;
                }

                // update settings
                BackendProfilesModel::setSetting($this->id, 'first_name', $txtFirstName->getValue());
                BackendProfilesModel::setSetting($this->id, 'last_name', $txtLastName->getValue());
                BackendProfilesModel::setSetting($this->id, 'gender', $ddmGender->getValue());
                BackendProfilesModel::setSetting($this->id, 'birth_date', $birthDate);
                BackendProfilesModel::setSetting($this->id, 'city', $txtCity->getValue());
                BackendProfilesModel::setSetting($this->id, 'country', $ddmCountry->getValue());

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

                $redirectUrl = BackendModel::createURLForAction('Edit') .
                    '&id=' . $this->id .
                    '&var=' . rawurlencode($values['display_name']) .
                    '&report='
                ;

                // notify new profile user
                if ($this->notifyProfile) {
                    BackendProfilesModel::notifyProfile($notifyValues);

                    $redirectUrl .= 'saved-and-notified';
                } else {
                    $redirectUrl .= 'saved';
                }

                // notify admin
                if ($this->notifyAdmin) {
                    BackendProfilesModel::notifyAdmin($notifyValues);
                }

                // everything is saved, so redirect to the overview
                $this->redirect($redirectUrl);
            }
        }
    }

    /**
     * Parse
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('notifyProfile', $this->notifyProfile);
    }
}
