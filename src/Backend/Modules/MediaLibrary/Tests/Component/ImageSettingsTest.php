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
            '800x600-crop-100',
            '800x600-resize-100',
            '800x600-100',
            '800x600-crop-20',
            '800x600-resize-20',
            '800x600-20',
            '800x-crop-100',
            '800x-resize-100',
            '800x-100',
            '800x-crop-20',
            '800x-resize-20',
            '800x-20',
            'x800-crop-100',
            'x800-resize-100',
            'x800-100',
            'x800-crop-20',
            'x800-resize-20',
            'x800-20',
            'x800-crop-20',
            'x800-resize-20',
        ];

        $this->falseFolderNames = [
            '800x600-crop',
            '800x600-resize',
            '800x600',
            '800x-crop',
            '800x-resize',
            '800x',
            'x800-crop',
            'x800-resize',
            'x800',
            'abcde',
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

    /**
     * @expectedException \Exception
     */
    public function testFalseFolderNamesFromString()
    {
        foreach ($this->falseFolderNames as $folderName) {
            /** @var ImageSettings $imageSettings */
            $imageSettings = ImageSettings::fromString(
                $folderName
            );

            $this->assertNotEquals($folderName, $imageSettings->toString());
        }
    }
}
