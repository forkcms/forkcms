<?php

namespace ForkCMS\Core\Domain\Header;

use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use Stringable;

/** Page title is a wrapper around the breadcrumbs class to make it possible to update the page title automatically */
final class PageTitle implements Stringable
{
    private ?string $overwrittenPageTitle = null;

    public function __construct(private readonly BreadcrumbCollection $breadcrumbs)
    {
    }

    public function __toString(): string
    {
        return $this->getPageTitle();
    }

    public function overwritePageTitle(string $pageTitle): void
    {
        $this->overwrittenPageTitle = $pageTitle;
    }

    public function getPageTitle(): string
    {
        return $this->overwrittenPageTitle ?? $this->breadcrumbs->asPageTitle();
    }
}
