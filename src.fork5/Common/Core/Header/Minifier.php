<?php

namespace Common\Core\Header;

use Common\Core\Model;
use MatthiasMullie\Minify;

final class Minifier
{
    /** @var Minify\Minify */
    private $minifyClass;

    /** @var string */
    private $basePath;

    /** @var string */
    private $cacheFileExtension;

    /** @var string */
    private $baseCacheUrl;

    /** @var string */
    private $baseCachePath;

    public function __construct(
        Minify\Minify $minifyClass,
        string $basePath,
        string $cacheFileExtension,
        string $baseCacheUrl,
        string $baseCachePath
    ) {
        $this->minifyClass = $minifyClass;
        $this->basePath = $basePath;
        $this->cacheFileExtension = $cacheFileExtension;
        $this->baseCacheUrl = $baseCacheUrl;
        $this->baseCachePath = $baseCachePath;
    }

    public static function css(string $basePath, string $baseCacheUrl, string $baseCachePath): self
    {
        return new self(new Minify\CSS(), $basePath, 'css', $baseCacheUrl, $baseCachePath);
    }

    public static function js(string $basePath, string $baseCacheUrl, string $baseCachePath): self
    {
        return new self(new Minify\JS(), $basePath, 'js', $baseCacheUrl, $baseCachePath);
    }

    /**
     * Minify the asset and return the assed for the minified version
     *
     * @param Asset $asset
     *
     * @return Asset
     */
    public function minify(Asset $asset): Asset
    {
        // don't minify when debug is true
        if (Model::getContainer()->getParameter('kernel.debug')) {
            return $asset;
        }

        $fileName = md5($asset->getFile()) . '.' . $this->cacheFileExtension;
        $filePath = $this->basePath . $asset->getFile();
        $cacheUrl = $this->baseCacheUrl . $fileName;
        $cachePath = $this->baseCachePath . $fileName;

        // check that file does not yet exist or has been updated already
        if (is_file($cachePath) && filemtime($filePath) <= filemtime($cachePath)) {
            return $asset->forCacheUrl($cacheUrl);
        }

        // clone the minify class since there is no way to clear the added files
        $minifier = clone $this->minifyClass;
        $minifier->add($filePath);
        $minifier->minify($cachePath);

        return $asset->forCacheUrl($cacheUrl);
    }
}
