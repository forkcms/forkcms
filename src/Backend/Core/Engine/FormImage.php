<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * This is our extended version of \SpoonFormFile
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class FormImage extends \SpoonFormImage
{
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
        $fs = new Filesystem();
        if (!$fs->exists($path . '/source')) {
            $fs->mkdir($path . '/source');
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
     * @param \SpoonTemplate $template The template to parse the element in.
     * @return string
     */
    public function parse(\SpoonTemplate $template = null)
    {
        // get upload_max_filesize
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        if ($uploadMaxFilesize === false) {
            $uploadMaxFilesize = 0;
        }

        // reformat if defined as an integer
        if (\SpoonFilter::isInteger($uploadMaxFilesize)) {
            $uploadMaxFilesize = $uploadMaxFilesize / 1024 . 'MB';
        }

        // reformat if specified in kB
        if (strtoupper(substr($uploadMaxFilesize, -1, 1)) == 'K') {
            $uploadMaxFilesize = substr($uploadMaxFilesize, 0, -1) . 'kB';
        }

        // reformat if specified in MB
        if (strtoupper(substr($uploadMaxFilesize, -1, 1)) == 'M') {
            $uploadMaxFilesize .= 'B';
        }

        // reformat if specified in GB
        if (strtoupper(substr($uploadMaxFilesize, -1, 1)) == 'G') {
            $uploadMaxFilesize .= 'B';
        }

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
                '[name]' => $this->attributes['name']
            )
        ) . ' />';

        // add help txt if needed
        if (!$this->hideHelpTxt) {
            $output .= '<span class="helpTxt">' .
                        sprintf(
                            Language::getMessage('HelpImageFieldWithMaxFileSize', 'core'),
                            $uploadMaxFilesize
                        ) . '</span>';
        }

        // parse to template
        if ($template !== null) {
            $template->assign('file' . \SpoonFilter::toCamelCase($this->attributes['name']), $output);
            $template->assign(
                'file' . \SpoonFilter::toCamelCase($this->attributes['name']) . 'Error',
                ($this->errors != '') ? '<span class="formError">' . $this->errors . '</span>' : ''
            );
        }

        return $output;
    }
}
