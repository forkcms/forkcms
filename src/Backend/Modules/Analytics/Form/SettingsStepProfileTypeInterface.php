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
final class SettingsStepProfileTypeInterface implements SettingsStepTypeInterface
{
    /** @var Form */
    private $form;

    /** @var ModulesSettings */
    private $settings;

    /** Google_Service_Analytics $googleServiceAnalytics */
    private $googleServiceAnalytics;

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
            'profile',
            $this->form->getField('profile')->getValue()
        );

        return true;
    }

    private function build(): void
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

    private function isValid(): bool
    {
        $this->form->getField('profile')->isFilled(Language::err('FieldIsRequired'));

        return $this->form->isCorrect();
    }
}
