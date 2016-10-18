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
    /** @var SettingsStepType */
    private $form;

    /**
     * @param string $name
     * @param ModulesSettings $settings
     * @param Google_Service_Analytics $googleServiceAnalytics
     */
    public function __construct($name, ModulesSettings $settings, Google_Service_Analytics $googleServiceAnalytics)
    {
        // we don't even have a auth config file yet, let the user upload it
        if ($settings->get('Analytics', 'certificate') === null) {
            $this->form = new SettingsStepAuthConfigFileType($name, $settings);

            return;
        }

        // we are authenticated! Let's see which account the user wants to use
        if ($settings->get('Analytics', 'account') === null) {
            $this->form = new SettingsStepAccountType($name, $settings, $googleServiceAnalytics);

            return;
        }

        // we have an account, but don't know which property to track
        if ($settings->get('Analytics', 'web_property_id') === null) {
            $this->form = new SettingsStepWebPropertyType($name, $settings, $googleServiceAnalytics);

            return;
        }

        // we have an account, but don't know which property to track
        if ($settings->get('Analytics', 'profile') === null) {
            $this->form = new SettingsStepProfileType($name, $settings, $googleServiceAnalytics);

            return;
        }

        $this->form = new Form($name);
    }

    /**
     * @param TwigTemplate $template
     */
    public function parse(TwigTemplate $template)
    {
        $this->form->parse($template);
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if ($this->form instanceof Form) {
            return false;
        }

        return $this->form->handle();
    }
}
