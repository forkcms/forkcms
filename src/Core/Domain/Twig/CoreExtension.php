<?php

namespace ForkCMS\Core\Domain\Twig;

use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use ForkCMS\Core\Domain\Header\ContentTitle;
use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Core\Domain\Header\PageTitle;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class CoreExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ContentTitle $contentTitle,
        private readonly PageTitle $pageTitle,
        private readonly BreadcrumbCollection $breadcrumbs,
        private readonly Header $header,
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'SITE_URL' => $_ENV['SITE_PROTOCOL'] . '://' . $_ENV['SITE_DOMAIN'],
            'SITE_PROTOCOL' => $_ENV['SITE_PROTOCOL'],
            'SITE_DOMAIN' => $_ENV['SITE_DOMAIN'],
            'SITE_MULTILINGUAL' => $_ENV['SITE_MULTILINGUAL'] === 'true',
            'CRLF' => "\n",
            'TAB' => "\t",
            'NOW' => time(),
            'PAGE_TITLE' => $this->pageTitle,
            'CONTENT_TITLE' => $this->contentTitle,
            'BREADCRUMBS' => $this->breadcrumbs,
            'JS_DATA' => $this->header->jsData,
            'JS_FILES' => $this->header->jsAssets,
            'CSS_FILES' => $this->header->cssAssets,
            'META' => $this->header->metaCollection,
        ];
    }
}
