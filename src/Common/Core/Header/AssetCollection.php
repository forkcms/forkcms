<?php

namespace Common\Core\Header;

use Common\Core\Twig\BaseTwigTemplate;

final class AssetCollection
{
    /** @var Minifier */
    private $minifier;

    /** @var Asset[] */
    private $assets = [];

    /** @var array[] */
    private $fileNames = [];

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
        if (in_array($asset->getFile(), $this->fileNames)) {
            return;
        }

        $sequence = (count($this->assets) > 0) ? max(array_keys($this->assets)) + 1 : 0;
        $asset->setSequence($sequence);

        $this->assets[] = $asset;
        $this->fileNames[] = $asset->getFile();
    }

    /**
     * @return Asset[]
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    public function parse(BaseTwigTemplate $template, string $key): void
    {
        usort(
            $this->assets,
            function (Asset $asset1, Asset $asset2) {
                $comparison = $asset1->getPriority()->compare($asset2->getPriority());

                if ($comparison === 0) {
                    $comparison = $asset1->getSequence() <=> $asset2->getSequence();
                }

                return $comparison;
            }
        );

        $template->assignGlobal(
            $key,
            $this->assets
        );
    }
}
