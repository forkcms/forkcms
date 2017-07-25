<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\Form;
use Backend\Core\Engine\TwigTemplate;
use Common\ModulesSettings;
use Google_Service_Analytics;

/**
 * A form to change the settings of the analytics module
 */
final class SettingsType
{
    /** @var SettingsStepTypeInterface|Form */
    private $form;

    public function __construct(
        string $name,
        ModulesSettings $settings,
        Google_Service_Analytics $googleServiceAnalytics
    ) {
        // we don't even have a auth config file yet, let the user upload it
        if ($settings->get('Analytics', 'certificate') === null) {
            $this->form = new SettingsStepAuthConfigFileTypeInterface($name, $settings);

            return;
        }

        // we are authenticated! Let's see which account the user wants to use
        if ($settings->get('Analytics', 'account') === null) {
            $this->form = new SettingsStepAccountTypeInterface($name, $settings, $googleServiceAnalytics);

            return;
        }

        // we have an account, but don't know which property to track
        if ($settings->get('Analytics', 'web_property_id') === null) {
            $this->form = new SettingsStepWebPropertyTypeInterface($name, $settings, $googleServiceAnalytics);

            return;
        }

        // we have an account, but don't know which property to track
        if ($settings->get('Analytics', 'profile') === null) {
            $this->form = new SettingsStepProfileTypeInterface($name, $settings, $googleServiceAnalytics);

            return;
        }

        $this->form = new Form($name);
    }

    public function parse(TwigTemplate $template): void
    {
        $this->form->parse($template);
    }

    public function handle(): bool
    {
        if ($this->form instanceof Form) {
            return false;
        }

        return $this->form->handle();
    }
}
