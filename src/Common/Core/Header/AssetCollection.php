<?php

namespace Common\Core\Header;

use Common\Core\Twig\BaseTwigTemplate;

final class AssetCollection
{
    /** @var Minifier */
    private $minifier;

    /** @var Asset[] */
    private $assets = [];

    public function __construct(Minifier $minifier)
    {
        $this->minifier = $minifier;
    }

    public function add(Asset $asset, bool $minify = true): void
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
     * @param bool $orderAssets
     *
     * @return Asset[]
     */
    public function getAssets($orderAssets = false): array
    {
        if ($orderAssets) {
            $this->orderAssets();
        }

        return $this->assets;
    }

    private function orderAssets(): void
    {
        usort(
            $this->assets,
            function (Asset $asset1, Asset $asset2) {
                return $asset1->compare($asset2);
            }
        );
    }

    public function parse(BaseTwigTemplate $template, string $key): void
    {
        $this->orderAssets();
        $template->assignGlobal(
            $key,
            $this->assets
        );
    }
}
