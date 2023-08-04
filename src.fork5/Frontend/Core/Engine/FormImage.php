<?php

namespace Frontend\Core\Engine;

use ForkCMS\Utility\Thumbnails;
use SpoonFormImage;
use Symfony\Component\Filesystem\Filesystem;
use Frontend\Core\Language\Language as FL;

/**
 * This is our extended version of \SpoonFormImage
 */
class FormImage extends SpoonFormImage
{
    /**
     * Constructor.
     *
     * @param string $name The name.
     * @param string $class The CSS-class to be used.
     * @param string $classError The CSS-class to be used when there is an error.
     *
     * @see SpoonFormFile::__construct()
     */
    public function __construct(
        string $name,
        string $class = 'inputFilefield',
        string $classError = 'inputFilefieldError'
    ) {
        // call the parent
        parent::__construct($name, $class, $classError);

        // mime type hinting
        $this->setAttribute('accept', 'image/*');
    }

    /**
     * Generate thumbnails based on the folders in the path
     * Use
     *  - 128x128 as folder name to generate an image that where the width will be 128px and the height will be 128px
     *  - 128x as folder name to generate an image that where the width will be 128px,
     *      the height will be calculated based on the aspect ratio.
     *  - x128 as folder name to generate an image that where the width will be 128px,
     *      the height will be calculated based on the aspect ratio.
     *
     * @param string $path
     * @param string $filename
     */
    public function generateThumbnails(string $path, string $filename): void
    {
        // create folder if needed
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path . '/source')) {
            $filesystem->mkdir($path . '/source');
        }

        // move the source file
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
            $this->isAllowedExtension(['jpg', 'jpeg', 'gif', 'png'], FL::err('JPGGIFAndPNGOnly'));
            $this->isAllowedMimeType(['image/jpeg', 'image/gif', 'image/png'], FL::err('JPGGIFAndPNGOnly'));
        }

        return $this->errors;
    }
}
