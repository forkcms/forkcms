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

    public function execute(): void
    {
        parent::execute();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    public function getData(): void
    {
        $this->notifyAdmin = $this->get('fork.settings')->get(
            $this->url->getModule(),
            'send_new_profile_admin_mail',
            false
        );

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

        // create form
        $this->form = new BackendForm('add');

        // create elements
        $this->form
            ->addText('email')
            ->setAttribute('type', 'email')
        ;
        $this->form->addPassword('password');
        $this->form->addText('display_name');
        $this->form->addText('first_name');
        $this->form->addText('last_name');
        $this->form->addText('city');
        $this->form->addDropdown('gender', $genderValues);
        $this->form->addDropdown('day', array_combine($days, $days));
        $this->form->addDropdown('month', $months);
        $this->form->addDropdown('year', array_combine($years, $years));
        $this->form->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()));

        // set default elements dropdowns
        $this->form->getField('gender')->setDefaultElement('');
        $this->form->getField('day')->setDefaultElement('');
        $this->form->getField('month')->setDefaultElement('');
        $this->form->getField('year')->setDefaultElement('');
        $this->form->getField('country')->setDefaultElement('');
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // get fields
            $txtEmail = $this->form->getField('email');
            $txtDisplayName = $this->form->getField('display_name');
            $txtPassword = $this->form->getField('password');
            $txtFirstName = $this->form->getField('first_name');
            $txtLastName = $this->form->getField('last_name');
            $txtCity = $this->form->getField('city');
            $ddmGender = $this->form->getField('gender');
            $ddmDay = $this->form->getField('day');
            $ddmMonth = $this->form->getField('month');
            $ddmYear = $this->form->getField('year');
            $ddmCountry = $this->form->getField('country');

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
            if ($this->form->isCorrect()) {
                $password = ($txtPassword->isFilled()) ?
                    $txtPassword->getValue() : BackendModel::generatePassword(8);

                // build item
                $values = [
                    'email' => $txtEmail->getValue(),
                    'registered_on' => BackendModel::getUTCDate(),
                    'display_name' => $txtDisplayName->getValue(),
                    'url' => BackendProfilesModel::getUrl($txtDisplayName->getValue()),
                    'last_login' => BackendModel::getUTCDate(null, 0),
                    'password' => BackendProfilesModel::encryptPassword($password),
                ];

                $this->id = BackendProfilesModel::insert($values);

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
                    [
                        'id' => $this->id,
                        'first_name' => $txtFirstName->getValue(),
                        'last_name' => $txtLastName->getValue(),
                        'unencrypted_password' => $password,
                    ]
                );

                $redirectUrl = BackendModel::createUrlForAction('Edit') .
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

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('notifyProfile', $this->notifyProfile);
    }
}
