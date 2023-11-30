<?php

namespace ForkCMS\Core\Domain\Header;

use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use Stringable;

/** Content title is a wrapper around the breadcrumbs class to make it possible to update the content title automatically */
final class ContentTitle implements Stringable
{
    private ?string $overwrittenContentTitle = null;

    private bool $hideContentTitle = false;

    public function __construct(private readonly BreadcrumbCollection $breadcrumbs)
    {
    }

    public function __toString(): string
    {
        return $this->getContentTitle();
    }

    public function overwriteContentTitle(string $contentTitle): void
    {
        $this->overwrittenContentTitle = $contentTitle;
    }

    public function hideContentTitle(bool $hideContentTitle): void
    {
        $this->hideContentTitle = $hideContentTitle;
    }

    public function getContentTitle(): ?string
    {
        if ($this->hideContentTitle) {
            return null;
        }

        return $this->overwrittenContentTitle ?? $this->breadcrumbs->asContentTitle();
    }
}
