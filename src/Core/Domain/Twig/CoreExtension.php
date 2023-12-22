<?php

namespace ForkCMS\Core\Domain\Twig;

use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use ForkCMS\Core\Domain\Header\ContentTitle;
use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Core\Domain\Header\PageTitle;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class CoreExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ContentTitle $contentTitle,
        private readonly PageTitle $pageTitle,
        private readonly BreadcrumbCollection $breadcrumbs,
        private readonly Header $header,
        private readonly TranslatorInterface $translator,
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
            'JS_FILES' => $this->header->jsFiles,
            'CSS_FILES' => $this->header->cssFiles,
            'META' => $this->header->meta,
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('visualiseBool', [$this, 'visualiseBool'], ['is_safe' => ['html']]),
        ];
    }

    public function visualiseBool(bool $bool, bool $reverse = false): string
    {
        if ($reverse) {
            $bool = !$bool;
        }

        if ($bool) {
            return '<strong title="' . $this->translator->trans('lbl.Yes') . '" style="color:green">&#10003;</strong>';
        }

        return '<strong title="' . $this->translator->trans('lbl.No') . '" style="color:red">&#10008;</strong>';
    }
}
