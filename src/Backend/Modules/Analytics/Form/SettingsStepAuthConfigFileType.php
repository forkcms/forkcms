<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\Form;
use Backend\Core\Language\Language;
use Backend\Core\Engine\TwigTemplate;
use Common\ModulesSettings;

/**
 * A form to change the settings of the analytics module
 */
final class SettingsStepAuthConfigFileType implements SettingsStepType
{
    /** @var Form */
    private $form;

    /** @var ModulesSettings */
    private $settings;

    /**
     * @param string $name
     * @param ModulesSettings $settings
     */
    public function __construct($name, ModulesSettings $settings)
    {
        $this->form = new Form($name);
        $this->settings = $settings;

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

        $certificate = base64_encode(file_get_contents($this->form->getField('certificate')->getTempFileName()));

        $this->settings->set(
            'Analytics',
            'certificate',
            $certificate
        );
        $this->settings->set(
            'Analytics',
            'email',
            $this->form->getField('email')->getValue()
        );

        return true;
    }

    /**
     * Build up the form
     */
    private function build()
    {
        $this->form->addFile('certificate');
        $this->form->addText('email');
    }

    /**
     * @return bool
     */
    private function isValid()
    {
        $fileField = $this->form->getField('certificate');
        $emailField = $this->form->getField('email');

        if ($fileField->isFilled(Language::err('FieldIsRequired'))) {
            $fileField->isAllowedExtension(
                ['p12'],
                Language::err('P12Only')
            );
        }
        $emailField->isFilled(Language::err('FieldIsRequired'));
        $emailField->isEmail(Language::err('EmailIsInvalid'));

        return $this->form->isCorrect();
    }
}
