<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class will be used to manage the breadcrumb
 */
class Breadcrumb extends KernelLoader
{
    /**
     * The items in the breadcrumb
     *
     * @var array
     */
    private $items = [];

    /**
     * TwigTemplate instance
     *
     * @var TwigTemplate
     */
    protected $template;

    /**
     * URL instance
     *
     * @var Url
     */
    protected $url;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->template = $this->getContainer()->get('templating');
        $this->url = $this->getContainer()->get('url');

        // store in reference
        $this->getContainer()->set('breadcrumb', $this);

        // get more information for the homepage
        $homeInfo = Navigation::getPageInfo(1);

        // add homepage as first item (with correct element)
        $this->addElement($homeInfo['navigation_title'], Navigation::getUrl(1));

        $this->addBreadcrumbsForPages($this->url->getPages());
    }

    private function addBreadcrumbsForPages(array $pages): void
    {
        $breadcrumbs = $this->getBreadcrumbsForPages($pages);

        // reverse so everything is in place
        krsort($breadcrumbs);

        array_map(
            function (array $breadcrumb) {
                $this->addElement($breadcrumb['title'], $breadcrumb['url']);
            },
            $breadcrumbs
        );
    }

    private function getBreadcrumbsForPages(array $pages): array
    {
        $breadcrumbs = [];
        $errorUrl = Navigation::getUrl(404);

        // loop pages
        while (!empty($pages)) {
            $url = implode('/', $pages);
            $menuId = Navigation::getPageId($url);
            $pageInfo = Navigation::getPageInfo($menuId);

            // if we don't have info for the page, a navigation title or if it is a direct action => skip the page
            if ($pageInfo === false
                || !isset($pageInfo['navigation_title'])
                || $pageInfo['tree_type'] === 'direct_action') {
                array_pop($pages);

                continue;
            }

            $pageUrl = Navigation::getUrl($menuId);

            // if this is the error-page, so we won't show an URL.
            if ($pageUrl === $errorUrl) {
                $pageUrl = null;
            }

            $breadcrumbs[] = ['title' => $pageInfo['navigation_title'], 'url' => $pageUrl];

            array_pop($pages);
        }

        return $breadcrumbs;
    }

    /**
     * @param string $title The label that will be used in the breadcrumb.
     * @param string $url The URL for this item.
     */
    public function addElement(string $title, string $url = null): void
    {
        $this->items[] = ['title' => $title, 'url' => $url];
    }

    /**
     * Clear all (or a specific) elements in the breadcrumb
     *
     * @param int|null $key If the key is provided it will be removed from the array,
     *                 otherwise the whole array will be cleared.
     */
    public function clear(int $key = null): void
    {
        // clear all
        if ($key === null) {
            $this->items = [];

            return;
        }

        // remove specific key
        unset($this->items[$key]);

        if (empty($this->items)) {
            return;
        }

        // resort, to avoid problems when parsing
        ksort($this->items);
        $this->items = array_values($this->items);
    }

    /**
     * Count number of breadcrumbs that are already added.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get all elements
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Parse the breadcrumb into the template
     */
    public function parse(): void
    {
        // assign
        $this->template->assignGlobal('breadcrumb', $this->items);
    }
}
