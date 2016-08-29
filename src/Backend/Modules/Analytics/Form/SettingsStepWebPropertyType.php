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
final class SettingsStepWebPropertyType implements SettingsStepType
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
            'web_property_id',
            $this->form->getField('web_property_id')->getValue()
        );

        return true;
    }

    /**
     * Build up the form
     */
    private function build()
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

    /**
     * @return bool
     */
    private function isValid()
    {
        $this->form->getField('web_property_id')->isFilled(Language::err('FieldIsRequired'));

        return $this->form->isCorrect();
    }
}
