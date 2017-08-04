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
final class SettingsStepWebPropertyTypeInterface implements SettingsStepTypeInterface
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
            'web_property_id',
            $this->form->getField('web_property_id')->getValue()
        );

        return true;
    }

    private function build(): void
    {
        $properties = $this->googleServiceAnalytics->management_webproperties->listManagementWebproperties(
            $this->settings->get('Analytics', 'account')
        );

        $propertiesForDropDown = [];
        foreach ($properties->getItems() as $property) {
            $propertiesForDropDown[$property->getId()] = $property->getName();
        }
        $this->form->addDropdown('web_property_id', $propertiesForDropDown);
    }

    private function isValid(): bool
    {
        $this->form->getField('web_property_id')->isFilled(Language::err('FieldIsRequired'));

        return $this->form->isCorrect();
    }
}
