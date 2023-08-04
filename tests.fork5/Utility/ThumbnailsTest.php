<?php

namespace ForkCMS\Tests\Utility;

use ForkCMS\Utility\Thumbnails;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ThumbnailsTest extends TestCase
{
    // test images
    private const IMAGE_SQUARE = 'square.png'; // (200x200)
    private const IMAGE_LANDSCAPE = 'landscape.png'; // (200x100)
    private const IMAGE_PORTRAIT = 'portrait.png'; // (100x200)
    private const IMAGE_SOURCES = [
        self::IMAGE_SQUARE => 'data:image/png;base64,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',
        self::IMAGE_LANDSCAPE => 'data:image/png;base64,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',
        self::IMAGE_PORTRAIT => 'data:image/png;base64,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',
    ];

    // Folders
    private const BASE_FOLDER = __DIR__ . '/../../var/cache/test/thumbnails';
    private const SOURCE_FOLDER = 'source';
    private const IMAGE_FOLDERS = [
        '50x50',
        '50x200',
        '50x300',
        '100x100',
        '100x200',
        '100x300',
        '200x50',
        '200x100',
        '200x200',
        '200x300',
        '300x50',
        '300x100',
        '300x200',
        '300x300',
        'x50',
        '50x',
        'x100',
        '100x',
        'x200',
        '200x',
        'x300',
        '300x',
    ];

    /** @var Thumbnails */
    private $thumbnails;

    /** @var string */
    private $realBaseFolder;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::createFolders();
        self::createImages();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::deleteFolders();
    }

    public function testGettingTheFolders(): void
    {
        $folderInfo = $this->thumbnails->getFolders(self::BASE_FOLDER);
        self::assertCount(count($folderInfo), $folderInfo);
        foreach ($folderInfo as $info) {
            self::assertArrayHasKey('dirname', $info, 'The array key dirname should exist');
            $folder = $info['dirname'];
            self::assertContains($folder, self::IMAGE_FOLDERS, 'The folder "' . $folder . '" should not exist');
            self::assertContains(
                [
                    'dirname' => $folder,
                    'path' => $this->realBaseFolder . '/' . $folder,
                    'url' => '',
                    'width' => $this->getWidthFromFolder($folder),
                    'height' => $this->getHeightFromFolder($folder),
                ],
                $folderInfo
            );
        }
    }

    public function testImagesAreGenerated(): void
    {
        foreach (self::IMAGE_SOURCES as $filename => $source) {
            // verify that the source image exists first
            self::assertTrue(is_file($this->realBaseFolder . '/' . self::SOURCE_FOLDER . '/' . $filename));

            // generate the thumbnails
            $this->thumbnails->generate(
                $this->realBaseFolder,
                $this->realBaseFolder . '/' . self::SOURCE_FOLDER . '/' . $filename
            );

            // check if the thumbnail got generated
            $this->assertImageExistsInThumbnailFolders($filename);

            foreach (self::IMAGE_FOLDERS as $imageFolder) {
                [$width, $height] = getimagesize($this->realBaseFolder . '/' . $imageFolder . '/' . $filename);
                $folderWidth = $this->getWidthFromFolder($imageFolder);
                $folderHeight = $this->getHeightFromFolder($imageFolder);
                if ($folderWidth === null) {
                    $folderWidth = (int) ($folderHeight * ($width/$height));
                }
                self::assertSame($width, $folderWidth);
                if ($folderHeight === null) {
                    $folderHeight = (int) ($folderWidth * ($height/$width));
                }
                self::assertSame($height, $folderHeight);
            }
        }
    }

    public function testImagesAreRemoved(): void
    {
        $fileSystem = new Filesystem();

        foreach (self::IMAGE_SOURCES as $filename => $source) {
            // verify that the source image exists first
            self::assertTrue(is_file($this->realBaseFolder . '/' . self::SOURCE_FOLDER . '/' . $filename));

            foreach (self::IMAGE_FOLDERS as $imageFolder) {
                $fileSystem->copy(
                    $this->realBaseFolder . '/' . self::SOURCE_FOLDER . '/' . $filename,
                    $this->realBaseFolder . '/' . $imageFolder . '/' . $filename
                );
            }

            // check if the thumbnail exist before removing
            $this->assertImageExistsInThumbnailFolders($filename);
            $this->thumbnails->delete($this->realBaseFolder, $filename);
            $this->assertImageDoesNotExistsInThumbnailFolders($filename);
        }
    }

    private function assertImageExistsInThumbnailFolders(string $filename): void
    {
        foreach (self::IMAGE_FOLDERS as $imageFolder) {
            self::assertFileExists($this->realBaseFolder . '/' . $imageFolder . '/' . $filename);
        }
    }

    private function assertImageDoesNotExistsInThumbnailFolders(string $filename): void
    {
        foreach (self::IMAGE_FOLDERS as $imageFolder) {
            self::assertFileNotExists($this->realBaseFolder . '/' . $imageFolder . '/' . $filename);
        }
    }

    private function getWidthFromFolder(string $folder): ?int
    {
        $width = (int) (explode('x', $folder)[0] ?? 0);

        return $width === 0 ? null : $width;
    }

    private function getHeightFromFolder(string $folder): ?int
    {
        $height = (int) (explode('x', $folder)[1] ?? 0);

        return $height === 0 ? null : $height;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->realBaseFolder = realpath(self::BASE_FOLDER);
        $this->thumbnails = new Thumbnails($this->realBaseFolder);
    }

    private static function createImages(): void
    {
        foreach (self::IMAGE_SOURCES as $name => $source) {
            file_put_contents(
                self::BASE_FOLDER . '/' . self::SOURCE_FOLDER . '/' . $name,
                base64_decode(str_replace('data:image/png;base64,', '', $source))
            );
        }
    }

    private static function createFolders(): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->mkdir(self::BASE_FOLDER . '/' . self::SOURCE_FOLDER);
        foreach (self::IMAGE_FOLDERS as $folder) {
            $fileSystem->mkdir(self::BASE_FOLDER . '/' . $folder);
        }
    }

    private static function deleteFolders(): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove(self::BASE_FOLDER);
    }
}
