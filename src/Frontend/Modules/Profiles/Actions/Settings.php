<?php

namespace Frontend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Frontend\Modules\Profiles\Engine\Profile;
use Symfony\Component\Intl\Intl as Intl;

/**
 * Change the settings for the current logged in profile.
 */
class Settings extends FrontendBaseBlock
{
    /**
     * FrontendForm instance.
     *
     * @var FrontendForm
     */
    private $form;

    /**
     * The current profile.
     *
     * @var Profile
     */
    private $profile;

    public function execute(): void
    {
        // profile logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            parent::execute();
            $this->getData();
            $this->loadTemplate();
            $this->buildForm();
            $this->validateForm();
            $this->parse();
        } else {
            // profile not logged in
            $this->redirect(
                FrontendNavigation::getUrlForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getUrlForBlock('Profiles', 'Settings'),
                307
            );
        }
    }

    private function getData(): void
    {
        // get profile
        $this->profile = FrontendProfilesAuthentication::getProfile();
    }

    private function buildForm(): void
    {
        // gender dropdown values
        $genderValues = [
            'male' => \SpoonFilter::ucfirst(FL::getLabel('Male')),
            'female' => \SpoonFilter::ucfirst(FL::getLabel('Female')),
        ];

        // birthdate dropdown values
        $days = range(1, 31);
        $months = \SpoonLocale::getMonths(LANGUAGE);
        $years = range(date('Y'), 1900);

        // get settings
        $birthDate = $this->profile->getSetting('birth_date');
        $nameChanges = (int) $this->profile->getSetting('display_name_changes');

        // get day, month and year
        if ($birthDate) {
            list($birthYear, $birthMonth, $birthDay) = explode('-', $birthDate);
        } else {
            // no birth date setting
            $birthDay = '';
            $birthMonth = '';
            $birthYear = '';
        }

        // create the form
        $this->form = new FrontendForm('updateSettings', null, null, 'updateSettingsForm');

        // create & add elements
        $this->form->addText('display_name', $this->profile->getDisplayName());
        $this->form->addText('first_name', $this->profile->getSetting('first_name'));
        $this->form->addText('last_name', $this->profile->getSetting('last_name'));
        $this->form->addText('email', $this->profile->getEmail());
        $this->form->addText('city', $this->profile->getSetting('city'));
        $this->form->addDropdown(
            'country',
            Intl::getRegionBundle()->getCountryNames(LANGUAGE),
            $this->profile->getSetting('country')
        );
        $this->form->addDropdown('gender', $genderValues, $this->profile->getSetting('gender'));
        $this->form->addDropdown('day', array_combine($days, $days), $birthDay);
        $this->form->addDropdown('month', $months, $birthMonth);
        $this->form->addDropdown('year', array_combine($years, $years), (int) $birthYear);

        // set default elements drop-downs
        $this->form->getField('gender')->setDefaultElement('');
        $this->form->getField('day')->setDefaultElement('');
        $this->form->getField('month')->setDefaultElement('');
        $this->form->getField('year')->setDefaultElement('');
        $this->form->getField('country')->setDefaultElement('');

        // set email disabled
        $this->form->getField('email')->setAttribute('disabled', 'disabled');

        // set avatar
        $this->form->addImage('avatar');

        // when user exceeded the number of name changes set field disabled
        if ($nameChanges >= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES) {
            $this->form->getField(
                'display_name'
            )->setAttribute('disabled', 'disabled');
        }
    }

    private function parse(): void
    {
        // have the settings been saved?
        if ($this->url->getParameter('sent') == 'true') {
            // show success message
            $this->template->assign('updateSettingsSuccess', true);
        }

        // assign avatar
        $avatar = $this->profile->getSetting('avatar');
        if (empty($avatar)) {
            $avatar = '';
        }
        $this->template->assign('avatar', $avatar);

        // parse the form
        $this->form->parse($this->template);

        // display name changes
        $this->template->assign('maxDisplayNameChanges', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES);
        $this->template->assign(
            'displayNameChangesLeft',
            FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES - $this->profile->getSetting('display_name_changes')
        );
    }

    private function validateForm(): void
    {
        // is the form submitted
        if ($this->form->isSubmitted()) {
            // get fields
            $txtDisplayName = $this->form->getField('display_name');
            $txtFirstName = $this->form->getField('first_name');
            $txtLastName = $this->form->getField('last_name');
            $txtCity = $this->form->getField('city');
            $ddmCountry = $this->form->getField('country');
            $ddmGender = $this->form->getField('gender');
            $ddmDay = $this->form->getField('day');
            $ddmMonth = $this->form->getField('month');
            $ddmYear = $this->form->getField('year');

            // get number of display name changes
            $nameChanges = (int) FrontendProfilesModel::getSetting($this->profile->getId(), 'display_name_changes');

            // has there been a valid display name change request?
            if ($this->profile->getDisplayName() !== $txtDisplayName->getValue() &&
                $nameChanges <= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES
            ) {
                // display name filled in?
                if ($txtDisplayName->isFilled(FL::getError('FieldIsRequired'))) {
                    // display name exists?
                    if (FrontendProfilesModel::existsDisplayName(
                        $txtDisplayName->getValue(),
                        $this->profile->getId()
                    )
                    ) {
                        // set error
                        $txtDisplayName->addError(FL::getError('DisplayNameExists'));
                    }
                }
            }

            // birthdate is not required but if one is filled we need all
            if ($ddmMonth->isFilled() || $ddmDay->isFilled() || $ddmYear->isFilled()) {
                // valid birth date?
                if (!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue())) {
                    // set error
                    $ddmYear->addError(FL::getError('DateIsInvalid'));
                }
            }

            // validate avatar when given
            $this->form->getField('avatar')->isFilled();

            // no errors
            if ($this->form->isCorrect()) {
                // init
                $values = [];
                $settings = [];

                // has there been a valid display name change request?
                if ($this->profile->getDisplayName() !== $txtDisplayName->getValue() &&
                    $nameChanges <= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES
                ) {
                    // get display name value
                    $values['display_name'] = $txtDisplayName->getValue();

                    // update url based on the new display name
                    $values['url'] = FrontendProfilesModel::getUrl(
                        $txtDisplayName->getValue(),
                        $this->profile->getId()
                    );

                    // update display name count
                    $settings['display_name_changes'] = $nameChanges + 1;
                }

                // update values
                if (!empty($values)) {
                    FrontendProfilesModel::update($this->profile->getId(), $values);
                }

                // build settings
                $settings['first_name'] = $txtFirstName->getValue();
                $settings['last_name'] = $txtLastName->getValue();
                $settings['city'] = $txtCity->getValue();
                $settings['country'] = $ddmCountry->getValue();
                $settings['gender'] = $ddmGender->getValue();

                // birthday is filled in
                if ($ddmYear->isFilled()) {
                    // mysql format
                    $settings['birth_date'] = $ddmYear->getValue() . '-';
                    $settings['birth_date'] .= str_pad($ddmMonth->getValue(), 2, '0', STR_PAD_LEFT) . '-';
                    $settings['birth_date'] .= str_pad($ddmDay->getValue(), 2, '0', STR_PAD_LEFT);
                } else {
                    // not filled in
                    $settings['birth_date'] = null;
                }

                // avatar
                $settings['avatar'] = $this->profile->getSetting('avatar');

                // create new filename
                if ($this->form->getField('avatar')->isFilled()) {
                    // field value
                    $settings['avatar'] = \SpoonFilter::urlise($this->profile->getDisplayName()) . '.' .
                                          $this->form->getField('avatar')->getExtension();

                    // move the file
                    $this->form->getField('avatar')->generateThumbnails(
                        FRONTEND_FILES_PATH . '/Profiles/Avatars/',
                        $settings['avatar']
                    );
                }

                // save settings
                $this->profile->setSettings($settings);

                // redirect
                $this->redirect(SITE_URL . FrontendNavigation::getUrlForBlock('Profiles', 'Settings') . '?sent=true');
            } else {
                $this->template->assign('updateSettingsHasFormError', true);
            }
        }
    }
}
