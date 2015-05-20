<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Analytics\GoogleClient\ClientFactory;

/**
 * This is the settings-action (default), it will be used to couple your analytics
 * account
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
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
        if ($this->get('fork.settings')->get($this->getModule(), 'auth_config') === null) {
            $this->form->addFile('auth_config');

            return;
        }

        // we don't have a token: redirect the user to Google to grant access
        if ($this->get('fork.settings')->get($this->getModule(), 'token') === null) {
            $client = $this->get('analytics.google_client');

            if ($this->getParameter('code') === null) {
                // make sure we receive a refresh token
                $client->setAccessType('offline');
                $this->redirect($client->createAuthUrl());
            } else {
                $client->authenticate($this->getParameter('code'));
                $this->get('fork.settings')->set($this->getModule(), 'token', $client->getAccessToken());
                $this->redirect(Model::createURLForAction('Settings'));
            }

            return;
        }

        // we are authenticated! Let's see which account the user wants to use
        if ($this->get('fork.settings')->get($this->getModule(), 'account') === null) {
            $analytics = $this->get('analytics.google_analytics_service');
            $accounts = $analytics->management_accounts->listManagementAccounts();
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
        if ($this->form->isSubmitted()) {
            if ($this->form->existsField('auth_config')) {
                $this->validateAuthConfigFileForm();
            }

            if ($this->form->existsField('account')) {
                $this->validateAccountForm();
            }

            if ($this->form->existsField('web_property_id')) {
                $this->validatePropertyForm();
            }

            if ($this->form->existsField('profile')) {
                $this->validateProfileForm();
            }
        }
    }

    private function validateAuthConfigFileForm()
    {
        $fileField = $this->form->getField('auth_config');

        if ($fileField->isFilled(Language::err('FieldIsRequired'))) {
            $fileField->isAllowedExtension(
                array('json'),
                Language::err('JsonOnly')
            );
        }

        if ($this->form->isCorrect()) {
            $this->get('fork.settings')->set(
                $this->getModule(),
                'auth_config',
                file_get_contents($fileField->getTempFileName())
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
