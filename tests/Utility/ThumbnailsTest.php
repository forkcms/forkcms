<?php

namespace ForkCMS\Tests\Utility\Thumbnails;

use ForkCMS\Utility\Thumbnails;
use PHPUnit\Framework\TestCase;

class ThumbnailsTest extends TestCase
{
    private const LANDSCAPE = '1000x500';
    private const PORTRAIT = '500x1000';
    private const SOURCE = 'source';
    private const SQUARE = '500x500';
    private const ONLY_WIDTH = '500x';
    private const ONLY_HEIGHT = 'x500';

    /** @var array  */
    private $folders = [];

    /** @var string  */
    private $filename;

    /** @var string  */
    private $rootPath;

    /** @var Thumbnails */
    private $thumbnails;

    private function createFolders()
    {
        foreach ($this->folders as $folder) {
            mkdir($folder);
        }
    }

    private function deleteFolders()
    {
        foreach ($this->folders as $folder) {
            rmdir($folder);
        }
    }

    private function deleteFiles()
    {
        unlink($this->rootPath . '/' . self::SOURCE . '/' . $this->filename);
    }

    /**
     * Put your temp image into your filesystem.
     * (Not good as unit tests must work with any system,
     * but my research about mocking resources gave me nothing)
     */
    protected function setUp()
    {
        // Init variables
        $sitePathWWW = __DIR__ . '/../../var/cache/test';
        $this->rootPath = realpath($sitePathWWW);
        $this->filename = '1.jpg';
        $this->thumbnails = new Thumbnails($sitePathWWW);
        $this->folders = [
            $this->rootPath . '/' . self::SOURCE,
            $this->rootPath . '/' . self::SQUARE,
            $this->rootPath . '/' . self::PORTRAIT,
            $this->rootPath . '/' . self::LANDSCAPE,
            $this->rootPath . '/' . self::ONLY_HEIGHT,
            $this->rootPath . '/' . self::ONLY_WIDTH,
        ];

        $this->createFolders();

        // Copy our file to /source folder
        copy(
            $this->rootPath . '/../../../src/Backend/Core/Installer/Data/images/' . $this->filename,
            $this->rootPath . '/' . self::SOURCE . '/' . $this->filename
        );
    }

    protected function tearDown()
    {
        $this->deleteFiles();
        $this->deleteFolders();
    }

    /**
     * We have one big test method, because setUp and tearDown are executed for every test method.
     */
    public function testClassMethods()
    {
        // Test thumbnail folders
        $this->assertEquals(
            [
                [
                    'dirname' => self::ONLY_WIDTH,
                    'path' => $this->rootPath . '/' . self::ONLY_WIDTH,
                    'url' => '',
                    'width' => (int) explode('x', self::ONLY_WIDTH)[0],
                    'height' => null,
                ],
                [
                    'dirname' => self::LANDSCAPE,
                    'path' => $this->rootPath . '/' . self::LANDSCAPE,
                    'url' => '',
                    'width' => (int) explode('x', self::LANDSCAPE)[0],
                    'height' => (int) explode('x', self::LANDSCAPE)[1],
                ],
                [
                    'dirname' => self::ONLY_HEIGHT,
                    'path' => $this->rootPath . '/' . self::ONLY_HEIGHT,
                    'url' => '',
                    'width' => null,
                    'height' => (int) explode('x', self::ONLY_HEIGHT)[1],
                ],
                [
                    'dirname' => self::PORTRAIT,
                    'path' => $this->rootPath . '/' . self::PORTRAIT,
                    'url' => '',
                    'width' => (int) explode('x', self::PORTRAIT)[0],
                    'height' => (int) explode('x', self::PORTRAIT)[1],
                ],
                [
                    'dirname' => self::SQUARE,
                    'path' => $this->rootPath . '/' . self::SQUARE,
                    'url' => '',
                    'width' => (int) explode('x', self::SQUARE)[0],
                    'height' => (int) explode('x', self::SQUARE)[0],
                ]
            ],
            $this->thumbnails->getFolders($this->rootPath)
        );

        // Test thumbnail generating
        $this->thumbnails->generate($this->rootPath, $this->rootPath . '/' . self::SOURCE . '/' . $this->filename);
        $this->assertTrue(is_file($this->rootPath . '/' . self::SQUARE . '/' . $this->filename));

        // Test thumbnail deleting
        $this->thumbnails->delete($this->rootPath, $this->filename);
        $this->assertFalse(is_file($this->rootPath . '/' . self::LANDSCAPE . '/' . $this->filename));
        $this->assertFalse(is_file($this->rootPath . '/' . self::PORTRAIT . '/' . $this->filename));
        $this->assertFalse(is_file($this->rootPath . '/' . self::SQUARE . '/' . $this->filename));
        $this->assertTrue(is_file($this->rootPath . '/' . self::SOURCE . '/' . $this->filename));
    }
}
