<?php

namespace ForkCMS\Core\Domain\Header\Asset;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, Asset> */
final class AssetCollection implements IteratorAggregate
{
    /** @var Asset[] */
    private array $assets = [];
    private bool $isOrdered = true;

    public function add(Asset $asset): void
    {
        // we already have it we don't need to add it again
        if (array_key_exists($asset->file, $this->assets)) {
            return;
        }

        $this->assets[$asset->file] = $asset;
        $this->isOrdered = false;
    }

    public function getOrdered(): array
    {
        if (!$this->isOrdered) {
            usort($this->assets, static fn (Asset $asset1, Asset $asset2) => $asset1->compare($asset2));
            $this->isOrdered = true;
        }

        return $this->assets;
    }

    public function getIterator(): Traversable
    {
        if (!$this->isOrdered) {
            usort($this->assets, static fn (Asset $asset1, Asset $asset2) => $asset1->compare($asset2));
            $this->isOrdered = true;
        }

        return new ArrayIterator($this->assets);
    }
}
