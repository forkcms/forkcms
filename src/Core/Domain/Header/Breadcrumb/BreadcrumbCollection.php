<?php

namespace ForkCMS\Core\Domain\Header\Breadcrumb;

use ArrayIterator;
use IteratorAggregate;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Traversable;

/**
 * @implements IteratorAggregate<Breadcrumb>
 */
final class BreadcrumbCollection implements IteratorAggregate
{
    /** @var Breadcrumb[]  */
    private array $items = [];

    private bool $shouldTranslate = false;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function add(Breadcrumb $breadcrumb): void
    {
        $this->shouldTranslate = $this->shouldTranslate || $breadcrumb->label instanceof TranslatableInterface;
        $this->items[] = $breadcrumb;
    }

    /**
     * @param int $key removes the element with the given key
     */
    public function remove(int $key): void
    {
        unset($this->items[$key]);

        if (count($this->items) === 0) {
            return;
        }

        // close the gap in the indexes to avoid problems when parsing
        $this->items = array_values($this->items);
    }

    public function removeLastBreadcrumb(): void
    {
        array_pop($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return Breadcrumb[] */
    public function getItems(): array
    {
        if ($this->shouldTranslate) {
            $this->items = array_map(
                fn (Breadcrumb $breadcrumb): Breadcrumb => $breadcrumb->withTranslatedLabel($this->translator),
                $this->items
            );
            $this->shouldTranslate = false;
        }
        return $this->items;
    }

    /** @implements Traversable<Breadcrumb> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getItems());
    }

    public function asPageTitle(): string
    {
        /** @var string[] $items the breadcrumbs are stringable but phpstan doesn't understand it */
        $items = $this->getItems();

        return implode(' | ', array_reverse(array_map(ucfirst(...), $items)));
    }

    public function asContentTitle(): string
    {
        /** @var string[] $items the breadcrumbs are stringable but phpstan doesn't understand it */
        $items = $this->getItems();

        return ucfirst(end($items));
    }
}
