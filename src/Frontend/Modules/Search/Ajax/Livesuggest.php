<?php

namespace Frontend\Modules\Search\Ajax;

use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Theme;
use Frontend\Core\Engine\TwigTemplate;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the live suggest-action, it will output a list of results for a certain search
 */
class Livesuggest extends FrontendBaseAJAXAction
{
    /** @var array */
    private $searchResults;

    /** @var int */
    private $limit;

    /** @var int */
    private $offset;

    /** @var int */
    private $requestedPage;

    /** @var array */
    private $pagination;

    /** @var string */
    private $searchTerm = '';

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var string */
    private $cacheKey;

    /** @var TwigTemplate */
    private $template;

    private function display(): void
    {
        $this->requestedPage = 1;
        $this->limit = (int) $this->get('fork.settings')->get('Search', 'overview_num_items', 20);
        $this->offset = ($this->requestedPage * $this->limit) - $this->limit;
        $this->cache = $this->get('cache.search');
        $this->cacheKey = implode(
            '_',
            [$this->getModule(), LANGUAGE, md5($this->searchTerm), $this->offset, $this->limit]
        );

        if (!$this->getCachedData()) {
            // no valid cache so we get fresh data
            $this->getRealData();
        }

        $this->parse();

        $this->output(
            Response::HTTP_OK,
            $this->template->render(
                '/Search/Layout/Templates/Results.html.twig',
                $this->template->getAssignedVariables()
            )
        );
    }

    public function execute(): void
    {
        parent::execute();
        $this->validateForm();

        if ($this->searchTerm === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'term-parameter is missing.');

            return;
        }

        $this->display();
    }

    private function getCachedData(): bool
    {
        if (!$this->searchTerm || $this->getContainer()->getParameter('kernel.debug')) {
            return false;
        }

        $cacheItem = $this->cache->getItem($this->cacheKey);
        if (!$cacheItem->isHit()) {
            return false;
        }

        ['pagination' => $this->pagination, 'items' => $this->searchResults] = $cacheItem->get();

        return true;
    }

    private function getRealData(): void
    {
        if (!$this->searchTerm) {
            return;
        }

        $this->searchResults = FrontendSearchModel::search($this->searchTerm, $this->limit, $this->offset);

        // populate count fields in pagination
        // this is done after actual search because some items might be
        // activated/deactivated (getTotal only does rough checking)
        $numberOfItems = FrontendSearchModel::getTotal($this->searchTerm);
        $this->pagination = [
            'url' => FrontendNavigation::getUrlForBlock('Search') . '?form=search&q=' . $this->searchTerm,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'requested_page' => $this->requestedPage,
            'num_items' => FrontendSearchModel::getTotal($this->searchTerm),
            'num_pages' => (int) ceil($numberOfItems / $this->limit)
        ];

        // num pages is always equal to at least 1
        if ($this->pagination['num_pages'] === 0) {
            $this->pagination['num_pages'] = 1;
        }

        // Don't save the result in the cache when debug is enabled
        if ($this->getContainer()->getParameter('kernel.debug')) {
            return;
        }

        $cacheItem = $this->cache->getItem($this->cacheKey);
        $cacheItem->expiresAfter(new DateInterval('PT1H'));
        $cacheItem->set(['pagination' => $this->pagination, 'items' => $this->searchResults]);
        $this->cache->save($cacheItem);
    }

    private function parse(): void
    {
        $this->template = $this->get('templating');

        if (!$this->searchTerm) {
            return;
        }

        $this->template->assign('searchResults', $this->searchResults);
        $this->template->assign('searchTerm', $this->searchTerm);

        $this->parsePagination();
    }

    private function parsePagination(): void
    {
        // init var
        $pagination = [];
        $showFirstPages = false;
        $showLastPages = false;
        $useQuestionMark = true;

        // validate pagination array
        switch (true) {
            case (!isset($this->pagination['limit'])):
                throw new FrontendException('no limit in the pagination-property.');
            case (!isset($this->pagination['offset'])):
                throw new FrontendException('no offset in the pagination-property.');
            case (!isset($this->pagination['requested_page'])):
                throw new FrontendException('no requested_page available in the pagination-property.');
            case (!isset($this->pagination['num_items'])):
                throw new FrontendException('no num_items available in the pagination-property.');
            case (!isset($this->pagination['num_pages'])):
                throw new FrontendException('no num_pages available in the pagination-property.');
            case (!isset($this->pagination['url'])):
                throw new FrontendException('no url available in the pagination-property.');
        }

        // should we use a questionmark or an ampersand
        if (mb_strpos($this->pagination['url'], '?') !== false) {
            $useQuestionMark = false;
        }

        // no pagination needed
        if ($this->pagination['num_pages'] < 1) {
            return;
        }

        // populate count fields
        $pagination['num_pages'] = $this->pagination['num_pages'];
        $pagination['current_page'] = $this->pagination['requested_page'];

        // as long as we are below page 5 we should show all pages starting from 1
        if ($this->pagination['requested_page'] < 6) {
            $pagesStart = 1;
            $pagesEnd = ($this->pagination['num_pages'] >= 6) ? 6 : $this->pagination['num_pages'];

            // show last pages
            if ($this->pagination['num_pages'] > 5) {
                $showLastPages = true;
            }
        } elseif ($this->pagination['requested_page'] >= ($this->pagination['num_pages'] - 4)) {
            // as long as we are 5 pages from the end we should show all pages till the end
            $pagesStart = ($this->pagination['num_pages'] - 5);
            $pagesEnd = $this->pagination['num_pages'];

            // show first pages
            if ($this->pagination['num_pages'] > 5) {
                $showFirstPages = true;
            }
        } else {
            // page 7
            $pagesStart = $this->pagination['requested_page'] - 2;
            $pagesEnd = $this->pagination['requested_page'] + 2;
            $showFirstPages = true;
            $showLastPages = true;
        }

        // show previous
        if ($this->pagination['requested_page'] > 1) {
            // build URL
            if ($useQuestionMark) {
                $url = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] - 1);
            } else {
                $url = $this->pagination['url'] . '&page=' . ($this->pagination['requested_page'] - 1);
            }

            // set
            $pagination['show_previous'] = true;
            $pagination['previous_url'] = $url;
        }

        // show first pages?
        if ($showFirstPages) {
            // init var
            $pagesFirstStart = 1;
            $pagesFirstEnd = 1;

            // loop pages
            for ($i = $pagesFirstStart; $i <= $pagesFirstEnd; ++$i) {
                // build URL
                if ($useQuestionMark) {
                    $url = $this->pagination['url'] . '?page=' . $i;
                } else {
                    $url = $this->pagination['url'] . '&page=' . $i;
                }

                // add
                $pagination['first'][] = ['url' => $url, 'label' => $i];
            }
        }

        // build array
        for ($i = $pagesStart; $i <= $pagesEnd; ++$i) {
            // init var
            $current = ($i == $this->pagination['requested_page']);

            // build URL
            if ($useQuestionMark) {
                $url = $this->pagination['url'] . '?page=' . $i;
            } else {
                $url = $this->pagination['url'] . '&page=' . $i;
            }

            // add
            $pagination['pages'][] = ['url' => $url, 'label' => $i, 'current' => $current];
        }

        // show last pages?
        if ($showLastPages) {
            // init var
            $pagesLastStart = $this->pagination['num_pages'];
            $pagesLastEnd = $this->pagination['num_pages'];

            // loop pages
            for ($i = $pagesLastStart; $i <= $pagesLastEnd; ++$i) {
                // build URL
                if ($useQuestionMark) {
                    $url = $this->pagination['url'] . '?page=' . $i;
                } else {
                    $url = $this->pagination['url'] . '&page=' . $i;
                }

                // add
                $pagination['last'][] = ['url' => $url, 'label' => $i];
            }
        }

        // show next
        if ($this->pagination['requested_page'] < $this->pagination['num_pages']) {
            // build URL
            if ($useQuestionMark) {
                $url = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] + 1);
            } else {
                $url = $this->pagination['url'] . '&page=' . ($this->pagination['requested_page'] + 1);
            }

            // set
            $pagination['show_next'] = true;
            $pagination['next_url'] = $url;
        }

        // multiple pages
        $pagination['multiple_pages'] = ($pagination['num_pages'] == 1) ? false : true;

        // assign pagination
        $this->template->assign('pagination', $pagination);
    }

    private function validateForm(): void
    {
        $charset = $this->getContainer()->getParameter('kernel.charset');
        $searchTerm = $this->getRequest()->request->get('term', '');
        $this->searchTerm = ($charset === 'utf-8')
            ? \SpoonFilter::htmlspecialchars($searchTerm) : \SpoonFilter::htmlentities($searchTerm);
    }
}
