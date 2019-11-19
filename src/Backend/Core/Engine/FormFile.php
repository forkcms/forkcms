<?php

namespace Backend\Core\Engine;

use SpoonFilter;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This is our extended version of \SpoonFormFile
 */
class FormFile extends \SpoonFormFile
{
    /**
     * Should the helpTxt span be hidden when parsing the field?
     *
     * @var bool
     */
    private $hideHelpTxt = false;

    /**
     * Hides (or shows) the help text when parsing the field.
     *
     * @param bool $on
     */
    public function hideHelpTxt($on = true): void
    {
        $this->hideHelpTxt = $on;
    }

    /**
     * Parses the html for this filefield.
     *
     * @param TwigTemplate $template The template to parse the element in.
     *
     * @throws \SpoonFormException
     *
     * @return string
     */
    public function parse($template = null): string
    {
        // name is required
        if ($this->attributes['name'] == '') {
            throw new \SpoonFormException('A name is required for a file field. Please provide a name.');
        }

        // start html generation
        $output = '<input type="file"';

        // add attributes
        $output .= $this->getAttributesHTML(
            [
                '[id]' => $this->attributes['id'],
                '[name]' => $this->attributes['name'],
            ]
        ) . ' />';


        // add help txt if needed
        if (!$this->hideHelpTxt) {
            // set aria describedby to link the help text with the field
            $this->attributes['aria-describedby'] = 'help' . ucfirst($this->attributes['id']);

            if (isset($this->attributes['extension'])) {
                $output .= '<small class="form-text text-muted" id="help' . ucfirst($this->attributes['id']) . '">'
                . sprintf(
                    BackendLanguage::getMessage('HelpFileFieldWithMaxFileSize', 'Core'),
                    $this->attributes['extension'],
                    Form::getUploadMaxFileSize()
                ) . '</small>';
            } else {
                $output .= '<small class="form-text text-muted" id="help' . ucfirst($this->attributes['id']) . '">'
                . sprintf(BackendLanguage::getMessage('HelpMaxFileSize'), Form::getUploadMaxFileSize())
                . '</small>';
            }
        }

        // parse to template
        if ($template !== null) {
            $template->assign('file' . SpoonFilter::toCamelCase($this->attributes['name']), $output);
            $template->assign(
                'file' . SpoonFilter::toCamelCase($this->attributes['name']) . 'Error',
                ($this->errors != '') ? '<span class="invalid-feedback">' . $this->errors . '</span>' : ''
            );
        }

        return $output;
    }

    /**
     * This function will return the errors. It is extended so we can do file checks automatically.
     *
     * @return string|null
     */
    public function getErrors(): ?string
    {
        // if the image is bigger then the allowed configuration it won't show up as filled but it is submitted
        // the empty check is added because otherwise this error is shown like 7 times
        if ($this->isSubmitted() && isset($_FILES[$this->getName()]['error']) && empty($this->errors)) {
            $imageError = $_FILES[$this->getName()]['error'];
            if ($imageError === UPLOAD_ERR_INI_SIZE && empty($this->errors)) {
                $this->addError(
                    SpoonFilter::ucfirst(sprintf(BackendLanguage::err('FileTooBig'), Form::getUploadMaxFileSize()))
                );
            }
        }

        return $this->errors;
    }
}
