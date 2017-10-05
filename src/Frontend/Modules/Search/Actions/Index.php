<?php

namespace Frontend\Modules\Search\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This action will display a form to search
 */
class Index extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $form;

    /**
     * Name of the cache file
     *
     * @var string
     */
    private $cacheFile;

    /**
     * The items
     *
     * @var array
     */
    private $items;

    /**
     * Limit of data to fetch
     *
     * @var int
     */
    private $limit;

    /**
     * Offset of data to fetch
     *
     * @var int
     */
    private $offset;

    /**
     * The requested page
     *
     * @var int
     */
    private $requestedPage;

    /**
     * The search term
     *
     * @var string
     */
    private $term = '';

    private function display(): void
    {
        $this->requestedPage = $this->url->getParameter('page', 'int', 1);
        $this->limit = $this->get('fork.settings')->get('Search', 'overview_num_items', 20);
        $this->offset = ($this->requestedPage * $this->limit) - $this->limit;
        $this->cacheFile = FRONTEND_CACHE_PATH . '/' . $this->getModule() . '/' .
                           LANGUAGE . '_' . md5($this->term) . '_' .
                           $this->offset . '_' . $this->limit . '.php';

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

    private function isCacheFileTooOld(): bool
    {
        $cacheInfo = @filemtime($this->cacheFile);

        return !$cacheInfo || $cacheInfo < strtotime('-1 hour');
    }

    private function getCachedData(): bool
    {
        if (!$this->term
            || !is_file($this->cacheFile)
            || $this->isCacheFileTooOld()
            || $this->getContainer()->getParameter('kernel.debug')) {
            return false;
        }

        /** @var array $pagination */
        /** @var array $items */
        require_once $this->cacheFile;

        // set info (received from cache)
        $this->pagination = $pagination;
        $this->items = $items;

        return true;
    }

    private function getRealData(): void
    {
        if (!$this->term) {
            return;
        }

        $this->items = FrontendSearchModel::search($this->term, $this->limit, $this->offset);

        // populate count fields in pagination
        // this is done after actual search because some items might be
        // activated/deactivated (getTotal only does rough checking)
        $numberOfItems = FrontendSearchModel::getTotal($this->term);
        $this->pagination = [
            'url' => FrontendNavigation::getUrlForBlock('Search') . '?form=search&q=' . $this->term,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'requested_page' => $this->requestedPage,
            'num_items' => FrontendSearchModel::getTotal($this->term),
            'num_pages' => (int) ceil($numberOfItems / $this->limit)
        ];

        // num pages is always equal to at least 1
        if ($this->pagination['num_pages'] === 0) {
            $this->pagination['num_pages'] = 1;
        }

        if ($this->requestedPage < 1 || $this->requestedPage > $this->pagination['num_pages']) {
            throw new NotFoundHttpException();
        }

        if ($this->getContainer()->getParameter('kernel.debug')) {
            return;
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $this->cacheFile,
            "<?php\n" . '$pagination = ' . var_export($this->pagination, true) . ";\n" . '$items = '
            . var_export($this->items, true) . ";\n?>"
        );
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

        if (!$this->term) {
            return;
        }

        $this->template->assign('searchResults', $this->items);
        $this->template->assign('searchTerm', $this->term);
        $this->parsePagination();
    }

    private function saveStatistics(): void
    {
        if (!$this->term) {
            return;
        }

        $previousTerm = FrontendModel::getSession()->get('searchTerm', '');
        FrontendModel::getSession()->set('searchTerm', '');

        // don't save the search term in the database if it is the same as the last time
        if ($previousTerm !== $this->term) {
            FrontendSearchModel::save(
                [
                    'term' => $this->term,
                    'language' => LANGUAGE,
                    'time' => FrontendModel::getUTCDate(),
                    'data' => serialize(['server' => $_SERVER]),
                    'num_results' => $this->pagination['num_items'],
                ]
            );
        }

        FrontendModel::getSession()->set('searchTerm', $this->term);
    }

    private function validateForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        $this->form->cleanupFields();
        $this->form->getField('q')->isFilled(FL::err('TermIsRequired'));

        if ($this->form->isCorrect()) {
            $this->term = $this->form->getField('q')->getValue();
        }
    }
}
