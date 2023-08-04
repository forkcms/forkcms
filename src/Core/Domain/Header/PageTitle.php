<?php

namespace ForkCMS\Core\Domain\Header;

use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use Stringable;

/** Page title is a wrapper around the breadcrumbs class to make it possible to update the page title automatically */
final class PageTitle implements Stringable
{
    public function __construct(private readonly BreadcrumbCollection $breadcrumbs)
    {
    }

    public function __toString(): string
    {
        static $pageTitle;
        if ($pageTitle === null) {
            $pageTitle = $this->breadcrumbs->asPageTitle();
        }

        return $pageTitle;
    }
}
