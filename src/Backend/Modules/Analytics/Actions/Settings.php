<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Google_Service_Exception;

/**
 * This is the settings-action (default), it will be used to couple your analytics
 * account
 */
final class Settings extends ActionIndex
{
    /**
     * The form instance
     *
     * @var Form
     */
    private $form;

    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    public function loadForm()
    {
        $this->form = new Form('settings');

        // we don't even have a auth config file yet, let the user upload it
        if ($this->get('fork.settings')->get($this->getModule(), 'certificate') === null) {
            $this->form->addFile('certificate');
            $this->form->addtext('email');

            return;
        }

        // we are authenticated! Let's see which account the user wants to use
        if ($this->get('fork.settings')->get($this->getModule(), 'account') === null) {
            $analytics = $this->get('analytics.google_analytics_service');
            try {
                $accounts = $analytics->management_accounts->listManagementAccounts();
            } catch (Google_Service_Exception $e) {
                $this->tpl->assign(
                    'email',
                    $this->get('fork.settings')->get($this->getModule(), 'email')
                );

                return $this->tpl->assign('noAccounts', true);
            }

            $accountsForDropdown = array();
            foreach ($accounts->getItems() as $account) {
                $accountsForDropdown[$account->getId()] = $account->getName();
            }
            $this->form->addDropdown('account', $accountsForDropdown);

            return;
        }

        // we have an account, but don't know which property to track
        if ($this->get('fork.settings')->get($this->getModule(), 'web_property_id') === null) {
            $analytics = $this->get('analytics.google_analytics_service');
            $properties = $analytics->management_webproperties
                ->listManagementWebproperties($this->get('fork.settings')->get($this->getModule(), 'account'))
            ;
            $propertiesForDropdown = array();
            foreach ($properties->getItems() as $property) {
                $propertiesForDropdown[$property->getId()] = $property->getName();
            }
            $this->form->addDropdown('web_property_id', $propertiesForDropdown);

            return;
        }

        // we have an account, but don't know which property to track
        if ($this->get('fork.settings')->get($this->getModule(), 'profile') === null) {
            $analytics = $this->get('analytics.google_analytics_service');
            $profiles = $analytics->management_profiles
                ->listManagementProfiles(
                    $this->get('fork.settings')->get($this->getModule(), 'account'),
                    $this->get('fork.settings')->get($this->getModule(), 'web_property_id')
                )
            ;
            $profilesForDropdown = array();
            foreach ($profiles->getItems() as $property) {
                $profilesForDropdown[$property->getId()] = $property->getName();
            }
            $this->form->addDropdown('profile', $profilesForDropdown);

            return;
        }
    }

    protected function parse()
    {
        parent::parse();

        $this->form->parse($this->tpl);
        if ($this->get('fork.settings')->get($this->getModule(), 'web_property_id')) {
            $this->tpl->assign(
                'web_property_id',
                $this->get('fork.settings')->get($this->getModule(), 'web_property_id')
            );
        }
        if ($this->get('fork.settings')->get($this->getModule(), 'profile')) {
            $this->tpl->assign(
                'profile',
                $this->get('fork.settings')->get($this->getModule(), 'profile')
            );
        }
    }

    private function validateForm()
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        if ($this->form->existsField('certificate')) {
            return $this->validateAuthConfigFileForm();
        }

        if ($this->form->existsField('account')) {
            return $this->validateAccountForm();
        }

        if ($this->form->existsField('web_property_id')) {
            return $this->validatePropertyForm();
        }

        if ($this->form->existsField('profile')) {
            return $this->validateProfileForm();
        }
    }

    private function validateAuthConfigFileForm()
    {
        $fileField = $this->form->getField('certificate');
        $emailField = $this->form->getField('email');

        if ($fileField->isFilled(Language::err('FieldIsRequired'))) {
            $fileField->isAllowedExtension(
                array('p12'),
                Language::err('P12Only')
            );
        }
        $emailField->isFilled(Language::err('FieldIsRequired'));
        $emailField->isEmail(Language::err('EmailIsInvalid'));

        if ($this->form->isCorrect()) {
            $this->get('fork.settings')->set(
                $this->getModule(),
                'certificate',
                base64_encode(file_get_contents($fileField->getTempFileName()))
            );
            $this->get('fork.settings')->set(
                $this->getModule(),
                'email',
                $emailField->getValue()
            );

            $this->redirect(Model::createURLForAction('Settings'));
        }
    }

    private function validateAccountForm()
    {
        $accountField = $this->form->getField('account');
        $accountField->isFilled(Language::err('FieldIsRequired'));

        if ($this->form->isCorrect()) {
            $this->get('fork.settings')->set(
                $this->getModule(),
                'account',
                $accountField->getValue()
            );

            $this->redirect(Model::createURLForAction('Settings'));
        }
    }

    private function validatePropertyForm()
    {
        $webPropertyField = $this->form->getField('web_property_id');
        $webPropertyField->isFilled(Language::err('FieldIsRequired'));

        if ($this->form->isCorrect()) {
            $this->get('fork.settings')->set(
                $this->getModule(),
                'web_property_id',
                $webPropertyField->getValue()
            );

            $this->redirect(Model::createURLForAction('Settings'));
        }
    }

    private function validateProfileForm()
    {
        $profileField = $this->form->getField('profile');
        $profileField->isFilled(Language::err('FieldIsRequired'));

        if ($this->form->isCorrect()) {
            $this->get('fork.settings')->set(
                $this->getModule(),
                'profile',
                $profileField->getValue()
            );

            $this->redirect(Model::createURLForAction('Settings'));
        }
    }
}
