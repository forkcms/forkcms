<?php

namespace Backend\Modules\MediaLibrary\Tests\Component;

use PHPUnit_Framework_TestCase;
use Backend\Modules\MediaLibrary\Component\ImageSettings;

class ImageSettingsTest extends PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $correctFolderNames;

    /** @var array */
    protected $falseFolderNames;

    public function setUp()
    {
        $this->correctFolderNames = [
            '800x600',
            '800x600-20',
            '800x600-crop',
            '800x600-crop-20',
            '800x',
            '800x-20',
            '800x-crop',
            '800x-crop-20',
            'x800',
            'x800-20',
            'x800-crop',
            'x800-crop-20',
        ];
    }

    /**
     * Test correct folder names
     */
    public function testCorrectFolderNamesFromString()
    {
        foreach ($this->correctFolderNames as $folderName) {
            /** @var ImageSettings $imageSettings */
            $imageSettings = ImageSettings::fromString(
                $folderName
            );

            $this->assertEquals($folderName, $imageSettings->toString());
        }
    }
}
