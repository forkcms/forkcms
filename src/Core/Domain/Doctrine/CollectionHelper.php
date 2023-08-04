<?php

namespace ForkCMS\Core\Domain\Doctrine;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class CollectionHelper
{
    /**
     * @template T
     *
     * @param Collection<int|string,T> $newCollection
     * @param Collection<int|string,T> $currentCollection
     * @param Closure(T):T $addCallback
     * @param Closure(T):T $removeCallback
     */
    public static function updateCollection(
        Collection $newCollection,
        Collection $currentCollection,
        Closure $addCallback,
        Closure $removeCallback
    ): void {
        $newCollection->map($addCallback);
        $currentCollection->filter(fn (/* @var T $item */ mixed $item) => !$newCollection->contains($item))->map(
            $removeCallback
        );
    }

    /**
     * @template T
     *
     * @param Collection<int|string, T>|null $collection
     *
     * @return ArrayCollection<int|string, T>
     */
    public static function toArrayCollection(Collection|null $collection): ArrayCollection
    {
        return new ArrayCollection($collection?->toArray() ?? []);
    }
}
