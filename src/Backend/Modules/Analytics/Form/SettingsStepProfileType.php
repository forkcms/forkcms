<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\Form;
use Backend\Core\Language\Language;
use Backend\Core\Engine\TwigTemplate;
use Common\ModulesSettings;
use Google_Service_Analytics;

/**
 * A form to change the settings of the analytics module
 */
final class SettingsStepProfileType implements SettingsStepType
{
    /** @var Form */
    private $form;

    /** @var ModulesSettings */
    private $settings;

    /** Google_Service_Analytics $googleServiceAnalytics */
    private $googleServiceAnalytics;

    /**
     * @param string $name
     * @param ModulesSettings $settings
     * @param Google_Service_Analytics $googleServiceAnalytics
     */
    public function __construct($name, ModulesSettings $settings, Google_Service_Analytics $googleServiceAnalytics)
    {
        $this->form = new Form($name);
        $this->settings = $settings;
        $this->googleServiceAnalytics = $googleServiceAnalytics;

        $this->build();
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
        $this->form->cleanupFields();

        if (!$this->form->isSubmitted() || !$this->isValid()) {
            return false;
        }

        $this->settings->set(
            'Analytics',
            'profile',
            $this->form->getField('profile')->getValue()
        );

        return true;
    }

    /**
     * Build up the form
     */
    private function build()
    {
        $profiles = $this->googleServiceAnalytics->management_profiles->listManagementProfiles(
            $this->settings->get('Analytics', 'account'),
            $this->settings->get('Analytics', 'web_property_id')
        );

        $profilesForDropDown = [];
        foreach ($profiles->getItems() as $property) {
            $profilesForDropDown[$property->getId()] = $property->getName();
        }
        $this->form->addDropdown('profile', $profilesForDropDown);
    }

    /**
     * @return bool
     */
    private function isValid()
    {
        $this->form->getField('profile')->isFilled(Language::err('FieldIsRequired'));

        return $this->form->isCorrect();
    }
}
