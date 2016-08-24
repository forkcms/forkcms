<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;

/**
 * This is our extended version of \SpoonFormFile
 */
class FormImage extends \SpoonFormImage
{
    /**
     * Constructor.
     *
     * @param    string            $name          The name.
     * @param    string [optional] $class         The CSS-class to be used.
     * @param    string [optional] $classError    The CSS-class to be used when there is an error.
     *
     * @see      SpoonFormFile::__construct()
     */
    public function __construct($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
    {
        // call the parent
        parent::__construct($name, $class, $classError);

        // mime type hinting
        $this->setAttribute('accept', 'image/*');
    }

    /**
     * Should the helpTxt span be hidden when parsing the field?
     *
     * @var    bool
     */
    private $hideHelpTxt = false;

    /**
     * Generate thumbnails based on the folders in the path
     * Use
     *  - 128x128 as foldername to generate an image that where the width will
     *      be 128px and the height will be 128px
     *  - 128x as foldername to generate an image that where the width will
     *      be 128px, the height will be calculated based on the aspect ratio.
     *  - x128 as foldername to generate an image that where the width will
     *      be 128px, the height will be calculated based on the aspect ratio.
     *
     * @param string $path
     * @param string $filename
     */
    public function generateThumbnails($path, $filename)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path . '/source')) {
            $filesystem->mkdir($path . '/source');
        }
        $this->moveFile($path . '/source/' . $filename);

        // generate the thumbnails
        Model::generateThumbnails($path, $path . '/source/' . $filename);
    }

    /**
     * This function will return the errors. It is extended so we can do image checks automatically.
     *
     * @return string
     */
    public function getErrors()
    {
        // do an image validation
        if ($this->isFilled()) {
            $this->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), Language::err('JPGGIFAndPNGOnly'));
            $this->isAllowedMimeType(array('image/jpeg', 'image/gif', 'image/png'), Language::err('JPGGIFAndPNGOnly'));
        }

        return $this->errors;
    }

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
     * @throws \SpoonFormException
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
            $output .= '<p class="help-block">' .
                        sprintf(
                            Language::getMessage('HelpImageFieldWithMaxFileSize', 'core'),
                            Form::getUploadMaxFileSize()
                        ) . '</p>';
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
