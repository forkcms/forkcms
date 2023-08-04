<?php

namespace Frontend\Modules\Search\Ajax;

use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the auto suggest-action, it will output a list of results for a certain search
 */
class Autosuggest extends FrontendBaseAJAXAction
{
    /** @var array */
    private $searchResults;

    /** @var int */
    private $limit;

    /** @var int */
    private $offset;

    /** @var int */
    private $requestedPage;

    /** @var string */
    private $searchTerm = '';

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var string */
    private $cacheKey;

    /** @var int */
    private $autoSuggestItemLength;

    /** @var array */
    private $pagination;

    private function display(): void
    {
        // set variables
        $this->requestedPage = 1;
        $this->limit = (int) $this->get('fork.settings')->get('Search', 'autosuggest_num_items', 10);
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
            'num_pages' => (int) ceil($numberOfItems / $this->limit),
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

    public function parse(): void
    {
        // more matches to be found than allowed?
        if ($this->pagination['num_items'] > count($this->searchResults)) {
            // remove last result (to add this reference)
            array_pop($this->searchResults);

            // add reference to full search results page
            $this->searchResults[] = [
                'title' => FL::lbl('More'),
                'text' => FL::msg('MoreResults'),
                'full_url' => FrontendNavigation::getUrlForBlock($this->getModule())
                              . '?form=search&q=' . $this->searchTerm,
            ];
        }

        $charset = $this->getContainer()->getParameter('kernel.charset');

        $this->output(
            Response::HTTP_OK,
            array_map(
                function (array $searchResult) use ($charset) {
                    if (empty($searchResult['text'])
                        || mb_strlen($searchResult['text']) <= $this->autoSuggestItemLength) {
                        return $searchResult;
                    }

                    $searchResult['text'] = mb_substr(
                        strip_tags($searchResult['text']),
                        0,
                        $this->autoSuggestItemLength,
                        $charset
                    ) . '…';

                    return $searchResult;
                },
                $this->searchResults
            )
        );
    }

    private function validateForm(): void
    {
        // set values
        $charset = $this->getContainer()->getParameter('kernel.charset');
        $searchTerm = $this->getRequest()->request->get('term', '');
        $this->searchTerm = ($charset === 'utf-8')
            ? \SpoonFilter::htmlspecialchars($searchTerm) : \SpoonFilter::htmlentities($searchTerm);
        $this->autoSuggestItemLength = $this->getRequest()->request->getInt('length', 50);
    }
}
