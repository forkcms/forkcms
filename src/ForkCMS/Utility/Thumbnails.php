<?php

namespace ForkCMS\Utility;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Thumbnails
{
    /** @var Finder */
    private $finder;

    /** @var Filesystem */
    private $filesystem;

    /** @var Imagine */
    private $imagine;

    /** @var string */
    private $sitePath;

    public function __construct(string $sitePath)
    {
        $this->sitePath = realpath($sitePath);
        $this->finder = new Finder();
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

        foreach ($this->finder->directories()->in($inPath) as $directory) {
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
        /** @var ImageInterface $image */
        $image = $this->imagine->open($forSourceFile);

        /** @var Box */
        $box = $image->getSize();

        // if the width & height are specified we should ignore the aspect ratio
        if ($folder['width'] !== null && $folder['height'] !== null) {
            // we scale on the smaller dimension
            if ($box->getWidth() > $box->getHeight()) {
                $width  = $box->getWidth() * ($folder['height']/$box->getHeight());
                $height =  $folder['height'];

                // we center the crop in relation to the width
                $cropPoint = new Point(max($width - $folder['width'], 0)/2, 0);
            } else {
                $width  = $folder['width'];
                $height =  $box->getHeight() * ($folder['width']/$box->getWidth());

                // we center the crop in relation to the height
                $cropPoint = new Point(0, max($height - $folder['height'], 0)/2);
            }

            // we scale the image to make the smaller dimension fit our resize box
            $image = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND);

            // and crop exactly to the box
            $image->crop($cropPoint, new Box($folder['width'], $folder['height']));
        } else {
            // redefine box because we need to calculate box size
            $box = ($folder['width'] !== null) ? $box->widen($folder['width']) : $box->heighten($folder['height']);

            // we use resize and not thumbnail, because thumbnail has memory leaks
            $image->resize($box);
        }

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

        $folders = [];
        $nameFilter = ($includeSourceFolder) ? 'source' : '/^([0-9]*)x([0-9]*)$/';

        foreach ($this->finder->directories()->in($inPath)->name($nameFilter)->depth('== 0') as $directory) {
            $dirname = $directory->getBasename();
            $chunks = explode('x', $dirname, 2);

            if (!$includeSourceFolder && count($chunks) !== 2) {
                continue;
            }

            $folders[] = [
                'dirname' => $dirname,
                'path' => $directory->getRealPath(),
                'width' => is_int($chunks[0]) ? (int) $chunks[0] : null,
                'height' => is_int($chunks[1]) ? (int) $chunks[1] : null,
                'url' => (mb_substr($inPath, 0, mb_strlen($this->sitePath)) === $this->sitePath)
                    ? mb_substr($inPath, mb_strlen($this->sitePath)) : ''
            ];
        }

        return $folders;
    }
}
