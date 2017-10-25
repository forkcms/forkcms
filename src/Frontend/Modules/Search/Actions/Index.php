<?php

namespace Frontend\Modules\Search\Actions;

use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Index extends FrontendBaseBlock
{
    /** @var FrontendForm */
    private $form;

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

    private function display(): void
    {
        $this->requestedPage = $this->url->getParameter('page', 'int', 1);
        $this->limit = $this->get('fork.settings')->get('Search', 'overview_num_items', 20);
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
        $this->loadTemplate();
        $this->buildForm();
        $this->validateForm();
        $this->display();
        $this->saveStatistics();
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

        if ($this->requestedPage < 1 || $this->requestedPage > $this->pagination['num_pages']) {
            throw new NotFoundHttpException();
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

    private function buildForm(): void
    {
        $this->form = new FrontendForm('search', null, 'get', null, false);

        $query = $this->getQuery();
        $this->form->addText('q', $query)->setAttributes(
            [
                'data-role' => 'fork-search-field',
                'data-autocomplete' => 'enabled',
                'data-live-suggest' => 'enabled',
            ]
        );

        $this->header->setCanonicalUrl($this->getCanonicalUrl($query));
    }

    private function getQuery(): string
    {
        if ($this->getRequest()->query->has('q')) {
            return $this->getRequest()->query->get('q', '');
        }

        // search query was submitted by our search widget
        $query = $this->getRequest()->query->get('q_widget', '');
        // set $_GET variable to keep SpoonForm happy
        // should be refactored out when Symfony form are implemented here
        $_GET['q'] = $query;

        return $query;
    }

    private function getCanonicalUrl(string $query): string
    {
        $canonicalUrl = SITE_URL . FrontendNavigation::getUrlForBlock('Search');
        if ($query === '') {
            return $canonicalUrl;
        }

        return $canonicalUrl . '?q=' . \SpoonFilter::htmlspecialchars($query);
    }

    private function parse(): void
    {
        $this->addJS('/js/vendors/typeahead.bundle.min.js', true, false);
        $this->addCSS('Search.css');

        $this->form->parse($this->template);

        if (!$this->searchTerm) {
            return;
        }

        $this->template->assign('searchResults', $this->searchResults);
        $this->template->assign('searchTerm', $this->searchTerm);
        $this->parsePagination();
    }

    private function saveStatistics(): void
    {
        if (!$this->searchTerm) {
            return;
        }

        $previousTerm = FrontendModel::getSession()->get('searchTerm', '');
        FrontendModel::getSession()->set('searchTerm', '');

        // don't save the search term in the database if it is the same as the last time
        if ($previousTerm !== $this->searchTerm) {
            FrontendSearchModel::save(
                [
                    'term' => $this->searchTerm,
                    'language' => LANGUAGE,
                    'time' => FrontendModel::getUTCDate(),
                    'data' => serialize(['server' => $_SERVER]),
                    'num_results' => $this->pagination['num_items'],
                ]
            );
        }

        FrontendModel::getSession()->set('searchTerm', $this->searchTerm);
    }

    private function validateForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        $this->form->cleanupFields();
        $this->form->getField('q')->isFilled(FL::err('TermIsRequired'));

        if ($this->form->isCorrect()) {
            $this->searchTerm = $this->form->getField('q')->getValue();
        }
    }
}
