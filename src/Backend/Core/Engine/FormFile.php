<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of \SpoonFormFile
 */
class FormFile extends \SpoonFormFile
{
    /**
     * Should the helpTxt span be hidden when parsing the field?
     *
     * @var    bool
     */
    private $hideHelpTxt = false;

    /**
     * Hides (or shows) the help text when parsing the field.
     *
     * @param bool $on
     */
    public function hideHelpTxt($on = true)
    {
        $this->hideHelpTxt = $on;
    }

    /**
     * Parses the html for this filefield.
     *
     * @param TwigTemplate $template The template to parse the element in.
     *
     * @return string
     */
    public function parse($template = null)
    {
        // name is required
        if ($this->attributes['name'] == '') {
            throw new \SpoonFormException('A name is required for a file field. Please provide a name.');
        }

        // start html generation
        $output = '<input type="file"';

        // add attributes
        $output .= $this->getAttributesHTML(
            array(
                '[id]' => $this->attributes['id'],
                '[name]' => $this->attributes['name'],
            )
        ) . ' />';

        // add help txt if needed
        if (!$this->hideHelpTxt) {
            if (isset($this->attributes['extension'])) {
                $output .= '<p class="help-block">' .
                           sprintf(
                               Language::getMessage('HelpFileFieldWithMaxFileSize', 'core'),
                               $this->attributes['extension'],
                               Form::getUploadMaxFileSize()
                           ) . '</p>';
            } else {
                $output .= '<p class="help-block">' .
                           sprintf(
                               Language::getMessage('HelpMaxFileSize'),
                               Form::getUploadMaxFileSize()
                           ) . '</p>';
            }
        }

        // parse to template
        if ($template !== null) {
            $template->assign('file' . \SpoonFilter::toCamelCase($this->attributes['name']), $output);
            $template->assign(
                'file' . \SpoonFilter::toCamelCase($this->attributes['name']) . 'Error',
                ($this->errors != '') ? '<span class="formError text-danger">' . $this->errors . '</span>' : ''
            );
        }

        return $output;
    }
}
