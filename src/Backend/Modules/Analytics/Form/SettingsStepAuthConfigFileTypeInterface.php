<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\Form;
use Backend\Core\Language\Language;
use Backend\Core\Engine\TwigTemplate;
use Common\ModulesSettings;

/**
 * A form to change the settings of the analytics module
 */
final class SettingsStepAuthConfigFileTypeInterface implements SettingsStepTypeInterface
{
    /** @var Form */
    private $form;

    /** @var ModulesSettings */
    private $settings;

    public function __construct(string $name, ModulesSettings $settings)
    {
        $this->form = new Form($name);
        $this->settings = $settings;

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

    private function build(): void
    {
        $this->form->addFile('certificate');
        $this->form->addText('email');
    }

    private function isValid(): bool
    {
        $fileField = $this->form->getField('certificate');
        $emailField = $this->form->getField('email');

        if ($fileField->isFilled(Language::err('FieldIsRequired'))) {
            $fileField->isAllowedExtension(['p12'], Language::err('P12Only'));
        }
        $emailField->isFilled(Language::err('FieldIsRequired'));
        $emailField->isEmail(Language::err('EmailIsInvalid'));

        return $this->form->isCorrect();
    }
}
