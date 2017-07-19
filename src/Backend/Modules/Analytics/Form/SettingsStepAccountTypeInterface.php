<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\Form;
use Backend\Core\Language\Language;
use Backend\Core\Engine\TwigTemplate;
use Common\ModulesSettings;
use Google_Service_Exception;
use Google_Service_Analytics;

/**
 * A form to change the settings of the analytics module
 */
final class SettingsStepAccountTypeInterface implements SettingsStepTypeInterface
{
    /** @var Form */
    private $form;

    /** @var ModulesSettings */
    private $settings;

    /** Google_Service_Analytics $googleServiceAnalytics */
    private $googleServiceAnalytics;

    /** @var bool */
    private $hasAccounts;

    public function __construct(
        string $name,
        ModulesSettings $settings,
        Google_Service_Analytics $googleServiceAnalytics
    ) {
        $this->form = new Form($name);
        $this->settings = $settings;
        $this->googleServiceAnalytics = $googleServiceAnalytics;

        $this->build();
    }

    public function parse(TwigTemplate $template): void
    {
        if (!$this->hasAccounts) {
            $template->assign('email', $this->settings->get('Analytics', 'email'));
            $template->assign('noAccounts', true);
        }

        $this->form->parse($template);
    }

    public function handle(): bool
    {
        $this->form->cleanupFields();

        if (!$this->form->isSubmitted() || !$this->isValid()) {
            return false;
        }

        $this->settings->set(
            'Analytics',
            'account',
            $this->form->getField('account')->getValue()
        );

        return true;
    }

    private function build(): void
    {
        try {
            $accounts = $this->googleServiceAnalytics->management_accounts->listManagementAccounts();
        } catch (Google_Service_Exception $e) {
            $this->hasAccounts = false;

            return;
        }

        $accountsForDropDown = [];
        foreach ($accounts->getItems() as $account) {
            $accountsForDropDown[$account->getId()] = $account->getName();
        }
        $this->form->addDropdown('account', $accountsForDropDown);

        $this->hasAccounts = true;
    }

    private function isValid(): bool
    {
        $this->form->getField('account')->isFilled(Language::err('FieldIsRequired'));

        return $this->form->isCorrect();
    }
}
