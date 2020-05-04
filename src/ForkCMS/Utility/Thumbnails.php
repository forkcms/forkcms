<?php

namespace ForkCMS\Utility;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @TODO make this class final
 */
class Thumbnails
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Imagine */
    private $imagine;

    /** @var string */
    private $sitePath;

    public function __construct(string $sitePath)
    {
        $this->sitePath = realpath($sitePath);
        $this->filesystem = new Filesystem();
        $this->imagine = new Imagine();
    }

    /**
     * Delete thumbnails based on the folders in the path
     *
     * @param string $inPath The path wherein the thumbnail-folders exist.
     * @param string|null $filename The filename to be deleted.
     */
    public function delete(string $inPath, ?string $filename): void
    {
        // if there is no image filename provided we can't do anything
        if ($filename === null || $filename === '') {
            return;
        }
        $finder = new Finder();
        foreach ($finder->directories()->in($inPath) as $directory) {
            $fileName = $directory->getRealPath() . '/' . $filename;
            if (is_file($fileName)) {
                $this->filesystem->remove($fileName);
            }
        }
    }

    /**
     * Generate thumbnails based on the folders in the path
     * Use
     *  - 128x128 as foldername to generate an image where the width will be
     *      128px and the height will be 128px
     *  - 128x as foldername to generate an image where the width will be
     *      128px, the height will be calculated based on the aspect ratio.
     *  - x128 as foldername to generate an image where the height will be
     *      128px, the width will be calculated based on the aspect ratio.
     *
     * @param string $inPath The path wherein the thumbnail-folders will be stored.
     * @param string $forSourceFile The location of the source file.
     */
    public function generate(string $inPath, string $forSourceFile): void
    {
        foreach ($this->getFolders($inPath) as $folder) {
            $this->generateThumbnail($folder, $forSourceFile);
        }
    }

    private function generateThumbnail(array $folder, string $forSourceFile): void
    {
        $image = $this->imagine->open($forSourceFile);
        $imageBox = $image->getSize();

        $resizeBox = $this->calculateResizeBox(
            $imageBox->getWidth(),
            $imageBox->getHeight(),
            $folder['width'],
            $folder['height']
        );

        // check if we need to upscale the image to be able to crop it
        if (!$imageBox->contains($resizeBox)) {
            $image->resize($resizeBox);
        }

        // crop the image
        $image = $image->thumbnail(
            new Box(
                $this->calculateDesiredWidth(
                    $folder['width'],
                    $folder['height'],
                    $imageBox->getWidth(),
                    $imageBox->getHeight()
                ),
                $this->calculateDesiredHeight(
                    $folder['width'],
                    $folder['height'],
                    $imageBox->getWidth(),
                    $imageBox->getHeight()
                )
            ),
            ImageInterface::THUMBNAIL_OUTBOUND
        );

        $image->save($folder['path'] . '/' . basename($forSourceFile));
    }

    /**
     * Get the folders
     *
     * @param string $inPath The path
     * @param bool $includeSourceFolder Should the source-folder be included in the return-array.
     *
     * @return array
     */
    public function getFolders(string $inPath, bool $includeSourceFolder = false): array
    {
        if (!$this->filesystem->exists($inPath)) {
            return [];
        }

        $folders = $includeSourceFolder ? $this->getFolders($inPath) : [];
        $nameFilter = ($includeSourceFolder) ? 'source' : '/^([0-9]*)x([0-9]*)$/';
        $finder = new Finder();

        foreach ($finder->directories()->in($inPath)->name($nameFilter)->depth('== 0') as $directory) {
            $dirname = $directory->getBasename();
            $chunks = explode('x', $dirname, 2);

            if (!$includeSourceFolder && count($chunks) !== 2) {
                continue;
            }

            $folders[$dirname] = [
                'dirname' => $dirname,
                'path' => $directory->getRealPath(),
                'width' => is_numeric($chunks[0]) ? (int) $chunks[0] : null,
                'height' => (array_key_exists(1, $chunks) && is_numeric($chunks[1])) ? (int) $chunks[1] : null,
                'url' => (0 === mb_strpos($inPath, $this->sitePath))
                    ? mb_substr($inPath, mb_strlen($this->sitePath)) : ''
            ];
        }

        return array_values($folders);
    }

    /**
     * Calculate the desired width based on the aspect ratio if needed
     *
     * @param int|null $desiredWidth
     * @param int|null $desiredHeight
     * @param int $currentWidth
     * @param int $currentHeight
     * @return int
     */
    private function calculateDesiredWidth(
        ?int $desiredWidth,
        ?int $desiredHeight,
        int $currentWidth,
        int $currentHeight
    ): int {
        if ($desiredWidth !== null) {
            return $desiredWidth;
        }

        if ($desiredHeight === null) {
            throw new \RuntimeException('A desired height is needed to calculate the desired width');
        }

        return $desiredHeight * ($currentWidth/$currentHeight);
    }

    /**
     * Calculate the desired height based on the aspect ratio if needed
     *
     * @param int|null $desiredWidth
     * @param int|null $desiredHeight
     * @param $currentWidth
     * @param $currentHeight
     * @return int
     */
    private function calculateDesiredHeight(
        ?int $desiredWidth,
        ?int $desiredHeight,
        int $currentWidth,
        int $currentHeight
    ): int {
        if ($desiredHeight !== null) {
            return $desiredHeight;
        }

        if ($desiredWidth === null) {
            throw new \RuntimeException('A desired width is needed to calculate the desired height');
        }

        return $desiredWidth * ($currentHeight/$currentWidth);
    }

    private function calculateResizeBox(
        int $width,
        int $height,
        ?int $desiredWidth,
        ?int $desiredHeight
    ): Box {
        $desiredWidth = $this->calculateDesiredWidth($desiredWidth, $desiredHeight, $width, $height);
        $desiredHeight = $this->calculateDesiredHeight($desiredWidth, $desiredHeight, $width, $height);

        // if the current width and height are already in the desired width and height we don't need to do anything
        if ($width >= $desiredWidth && $height >= $desiredHeight) {
            return new Box($width, $height);
        }

        // handle width too small
        if ($width < $desiredWidth) {
            $scaleRatio = $desiredWidth/$width;
            $height *= $scaleRatio;
            $width *= $scaleRatio;
        }

        // handle height too small
        if ($height < $desiredHeight) {
            $scaleRatio = $desiredHeight/$height;
            $height *= $scaleRatio;
            $width *= $scaleRatio;
        }

        return new Box($width, $height);
    }
}
