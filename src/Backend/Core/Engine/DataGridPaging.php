<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Language\Language as BackendLanguage;
use iSpoonDatagridPaging;

/**
 * This is our implementation of iSpoonDatagridPaging
 */
final class DataGridPaging implements iSpoonDatagridPaging
{
    /** @var int */
    private $currentPage;

    /** @var int */
    private $totalNumberOfPages;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $orderByColumn;

    /** @var string */
    private $sortingDirection;

    /** @var int */
    private $resultsPerPage;

    /** @var int */
    private $totalNumberOfResults;

    /** @var int */
    private $offset;

    /**
     * Builds & returns the pagination
     *
     * @param string $baseUrl
     * @param int $offset
     * @param string $orderByColumn The name of the column to sort on.
     * @param string $sortingDirection The sorting method, possible values are: asc, desc.
     * @param int $totalNumberOfResults
     * @param int $resultsPerPage The items per page.
     * @param bool $debug
     * @param string $compileDirectory
     *
     * @return string
     */
    public static function getContent(
        $baseUrl,
        $offset,
        $orderByColumn,
        $sortingDirection,
        $totalNumberOfResults,
        $resultsPerPage,
        $debug = true,
        $compileDirectory = null
    ) {
        return (new self(
            $baseUrl,
            $offset,
            $orderByColumn,
            $sortingDirection,
            $totalNumberOfResults,
            $resultsPerPage
        ))->getHtml($debug, $compileDirectory);
    }

    /**
     * @param string $baseUrl
     * @param int $offset
     * @param string|null $orderByColumn The name of the column to sort on.
     * @param string $sortingDirection The sorting method, possible values are: asc, desc.
     * @param int $totalNumberOfResults
     * @param int $resultsPerPage The items per page.
     */
    private function __construct(
        string $baseUrl,
        int $offset,
        $orderByColumn,
        string $sortingDirection,
        int $totalNumberOfResults,
        int $resultsPerPage
    ) {
        $this->baseUrl = $baseUrl;
        $this->offset = $offset;
        $this->orderByColumn = $orderByColumn;
        $this->sortingDirection = $sortingDirection;
        $this->resultsPerPage = $resultsPerPage;
        $this->totalNumberOfResults = $totalNumberOfResults;
        $this->currentPage = (int) ceil($offset / $resultsPerPage) + 1;
        $this->totalNumberOfPages = (int) ceil($totalNumberOfResults / $resultsPerPage);
    }

    /**
     * @param bool $debug
     * @param string|null $compileDirectory
     *
     * @return string
     */
    public function getHtml($debug = true, string $compileDirectory = null)
    {
        // if there is just one page we don't need paging
        if ($this->totalNumberOfResults < $this->resultsPerPage) {
            return '';
        }

        // load template
        $tpl = new \SpoonTemplate();

        // compile directory
        $tpl->setCompileDirectory($compileDirectory ?? __DIR__);

        // force compiling
        $tpl->setForceCompile((bool) $debug);

        $tpl->assign('pagination', $this->getTemplateData());

        $tpl->assign('previousLabel', BackendLanguage::lbl('PreviousPage'));
        $tpl->assign('nextLabel', BackendLanguage::lbl('NextPage'));
        $tpl->assign('goToLabel', BackendLanguage::lbl('GoToPage'));

        return $tpl->getContent(BACKEND_CORE_PATH . '/Layout/Templates/DatagridPaging.tpl');
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    private function getUrlForOffset(int $offset): string
    {
        return str_replace(
            ['[offset]', '[order]', '[sort]'],
            [$offset, $this->orderByColumn, $this->sortingDirection],
            $this->baseUrl
        );
    }

    /**
     * @return array
     */
    private function getTemplateData(): array
    {
        return [
            'num_pages' => $this->totalNumberOfPages,
            'current_page' => $this->currentPage,
            'multiple_pages' => $this->totalNumberOfPages !== 1,
            'show_previous' => $this->showPreviousLink(),
            'previous_url' => $this->getPreviousLink(),
            'first' => $this->getFirstPages(),
            'pages' => $this->getPages($this->getPagesStart(), $this->getPagesEnd()),
            'last' => $this->getLastPages(),
            'show_next' => $this->showNextLink(),
            'next_url' => $this->getNextLink(),
        ];
    }

    /**
     * @return bool
     */
    private function showNextLink(): bool
    {
        return $this->currentPage < $this->totalNumberOfPages;
    }

    /**
     * @return string
     */
    private function getNextLink(): string
    {
        if (!$this->showNextLink()) {
            return '';
        }

        return $this->getUrlForOffset($this->offset + $this->resultsPerPage);
    }

    /**
     * @return bool
     */
    private function showPreviousLink(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return string
     */
    private function getPreviousLink(): string
    {
        if (!$this->showPreviousLink()) {
            return '';
        }

        return $this->getUrlForOffset($this->offset - $this->resultsPerPage);
    }

    /**
     * @return int
     */
    private function getPagesStart(): int
    {
        // as long as we have more then 5 pages and are 5 pages from the end we should show all pages till the end
        if ($this->currentPage > 5 && $this->currentPage >= ($this->totalNumberOfPages - 4)) {
            if ($this->totalNumberOfPages === 6) {
                return 1;
            }

            return $this->totalNumberOfPages > 7 ? $this->totalNumberOfPages - 5 : $this->totalNumberOfPages - 6;
        }

        if ($this->currentPage <= 5) {
            return 1;
        }

        return $this->currentPage - 2;
    }

    /**
     * @return int
     */
    private function getPagesEnd(): int
    {
        // as long as we have more then 5 pages and are 5 pages from the end we should show all pages till the end
        if ($this->currentPage > 5 && $this->currentPage >= ($this->totalNumberOfPages - 4)) {
            return $this->totalNumberOfPages;
        }

        if ($this->currentPage <= 5) {
            if ($this->totalNumberOfPages === 7) {
                // when we have 7 pages, show 7 as end
                return 7;
            }
            if ($this->totalNumberOfPages <= 6) {
                // when we have less then 6 pages, show the maximum page
                return $this->totalNumberOfPages;
            }

            return 6;
        }

        return $this->currentPage + 2;
    }

    /**
     * @return bool
     */
    private function showFirstPages(): bool
    {
        if ($this->currentPage > 5 && $this->currentPage >= ($this->totalNumberOfPages - 4)) {
            return $this->totalNumberOfPages > 7;
        }

        return $this->currentPage > 5;
    }

    /**
     * @return bool
     */
    private function showLastPages(): bool
    {
        if ($this->currentPage <= 5) {
            return $this->totalNumberOfPages > 7;
        }

        return false;
    }

    /**
     * @return array
     */
    private function getFirstPages(): array
    {
        if (!$this->showFirstPages()) {
            return [];
        }

        return $this->getPages(1, 2);
    }

    /**
     * @param int $start
     * @param int $end
     *
     * @return array
     */
    private function getPages(int $start, int $end): array
    {
        $pages = [];

        for ($i = $start; $i <= $end; ++$i) {
            $pages[] = [
                'url' => $this->getUrlForOffset($this->resultsPerPage * $i - $this->resultsPerPage),
                'label' => $i,
                'current' => $i === $this->currentPage,
            ];
        }

        return $pages;
    }

    /**
     * @return array
     */
    private function getLastPages(): array
    {
        if (!$this->showLastPages()) {
            return [];
        }

        return $this->getPages($this->totalNumberOfPages - 1, $this->totalNumberOfPages);
    }
}
