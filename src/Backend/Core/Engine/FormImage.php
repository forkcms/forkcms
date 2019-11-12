<?php

namespace Backend\Core\Engine;

use ForkCMS\Utility\Thumbnails;
use SpoonFilter;
use SpoonFormImage;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This is our extended version of \SpoonFormFile
 */
class FormImage extends SpoonFormImage
{
    /**
     * Constructor.
     *
     * @param string            $name          The name.
     * @param string [optional] $class         The CSS-class to be used.
     * @param string [optional] $classError    The CSS-class to be used when there is an error.
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
     * @var bool
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
    public function generateThumbnails($path, $filename): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path . '/source')) {
            $filesystem->mkdir($path . '/source');
        }
        $this->moveFile($path . '/source/' . $filename);

        // generate the thumbnails
        Model::get(Thumbnails::class)->generate($path, $path . '/source/' . $filename);
    }

    /**
     * This function will return the errors. It is extended so we can do image checks automatically.
     *
     * @return string|null
     */
    public function getErrors(): ?string
    {
        // do an image validation
        if ($this->isFilled()) {
            $this->isAllowedExtension(['jpg', 'jpeg', 'gif', 'png'], BackendLanguage::err('JPGGIFAndPNGOnly'));
            $this->isAllowedMimeType(['image/jpeg', 'image/gif', 'image/png'], BackendLanguage::err('JPGGIFAndPNGOnly'));
        }

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

        // each image field should have fork data role for image preview
        $this->attributes['data-fork-cms-role'] = 'image-field';

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

            $output .= '<small class="form-text text-muted" id="help' . ucfirst($this->attributes['id']) . '">'
            . sprintf(
                BackendLanguage::getMessage('HelpImageFieldWithMaxFileSize', 'Core'),
                Form::getUploadMaxFileSize()
            ) . '</small>';
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
}
