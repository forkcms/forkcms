<?php

namespace Common\Core\Header;

use Common\Core\Twig\BaseTwigTemplate;

final class AssetCollection
{
    /** @var Minifier */
    private $minifier;

    /** @var Asset[] */
    private $assets = [];

    /**
     * @param Minifier $minifier
     */
    public function __construct(Minifier $minifier)
    {
        $this->minifier = $minifier;
    }

    /**
     * @param Asset $asset
     * @param bool $minify
     */
    public function add(Asset $asset, $minify = true)
    {
        if ($minify) {
            $asset = $this->minifier->minify($asset);
        }

        // we already have it we don't need to add it again
        if (array_key_exists($asset->getFile(), $this->assets)) {
            return;
        }

        $this->assets[$asset->getFile()] = $asset;
    }

    /**
     * @return Asset[]
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param BaseTwigTemplate $template
     * @param string $key
     */
    public function parse(BaseTwigTemplate $template, string $key)
    {
        usort(
            $this->assets,
            function (Asset $asset1, Asset $asset2) {
                return $asset1->getPriority()->compare($asset2->getPriority());
            }
        );

        $template->assignGlobal(
            $key,
            $this->assets
        );
    }
}
