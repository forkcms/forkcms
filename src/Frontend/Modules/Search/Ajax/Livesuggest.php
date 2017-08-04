<?php

namespace Frontend\Modules\Search\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\TwigTemplate;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the live suggest-action, it will output a list of results for a certain search
 */
class Livesuggest extends FrontendBaseAJAXAction
{
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
     * The pagination array
     * It will hold all needed parameters, some of them need initialization.
     *
     * @var array
     */
    protected $pagination = [
        'limit' => 20,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null,
    ];

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

    /**
     * @var TwigTemplate
     */
    private $template;

    private function display(): void
    {
        // set variables
        $this->requestedPage = 1;
        $this->limit = (int) $this->get('fork.settings')->get('Search', 'overview_num_items', 20);
        $this->offset = ($this->requestedPage * $this->limit) - $this->limit;
        $this->cacheFile = FRONTEND_CACHE_PATH . '/' . $this->getModule() . '/' .
                           LANGUAGE . '_' . md5($this->term) . '_' .
                           $this->offset . '_' . $this->limit . '.php';

        // load the cached data
        if (!$this->getCachedData()) {
            // ... or load the real data
            $this->getRealData();
        }

        // parse
        $this->parse();

        // output
        $this->output(
            Response::HTTP_OK,
            $this->template->render(FRONTEND_PATH . '/Modules/Search/Layout/Templates/Results.html.twig')
        );
    }

    public function execute(): void
    {
        parent::execute();
        $this->validateForm();
        $this->display();
    }

    /**
     * Load the cached data
     *
     * @todo refactor me
     *
     * @return bool
     */
    private function getCachedData(): bool
    {
        // no search term = no search
        if (!$this->term) {
            return false;
        }

        // debug mode = no cache
        if ($this->getContainer()->getParameter('kernel.debug')) {
            return false;
        }

        // check if cache file exists
        if (is_file($this->cacheFile)) {
            return false;
        }

        // get cache file modification time
        $cacheInfo = @filemtime($this->cacheFile);

        // check if cache file is recent enough (1 hour)
        if (!$cacheInfo || $cacheInfo < strtotime('-1 hour')) {
            return false;
        }

        // include cache file
        require_once $this->cacheFile;

        // set info
        $this->pagination = $pagination;
        $this->items = $items;

        return true;
    }

    private function getRealData(): void
    {
        // no search term = no search
        if (!$this->term) {
            return;
        }

        // set url
        $this->pagination['url'] = FrontendNavigation::getUrlForBlock('Search') . '?form=search&q=' . $this->term;

        // populate calculated fields in pagination
        $this->pagination['limit'] = $this->limit;
        $this->pagination['offset'] = $this->offset;
        $this->pagination['requested_page'] = $this->requestedPage;

        // get items
        $this->items = FrontendSearchModel::search(
            $this->term,
            $this->pagination['limit'],
            $this->pagination['offset']
        );

        // populate count fields in pagination
        // this is done after actual search because some items might be
        // activated/deactivated (getTotal only does rough checking)
        $this->pagination['num_items'] = FrontendSearchModel::getTotal($this->term);
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // num pages is always equal to at least 1
        if ($this->pagination['num_pages'] == 0) {
            $this->pagination['num_pages'] = 1;
        }

        // error if the request page doesn't exist
        if ($this->requestedPage > $this->pagination['num_pages'] || $this->requestedPage < 1) {
            throw new FrontendException('the request page doesn\'t exist');
        }

        // debug mode = no cache
        if (!$this->getContainer()->getParameter('kernel.debug')) {
            // set cache content
            $filesystem = new Filesystem();
            $filesystem->dumpFile(
                $this->cacheFile,
                "<?php\n" . '$pagination = ' . var_export($this->pagination, true) . ";\n" . '$items = ' . var_export(
                    $this->items,
                    true
                ) . ";\n?>"
            );
        }
    }

    private function parse(): void
    {
        $this->template = $this->get('templating');

        // no search term = no search
        if (!$this->term) {
            return;
        }

        // assign articles
        $this->template->assign('searchResults', $this->items);
        $this->template->assign('searchTerm', $this->term);

        // parse the pagination
        $this->parsePagination();
    }

    protected function parsePagination(): void
    {
        // init var
        $pagination = null;
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
                $url = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] - 1);
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
                    $url = $this->pagination['url'] . '&amp;page=' . $i;
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
                $url = $this->pagination['url'] . '&amp;page=' . $i;
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
                    $url = $this->pagination['url'] . '&amp;page=' . $i;
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
                $url = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] + 1);
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
        // set search term
        $charset = $this->getContainer()->getParameter('kernel.charset');
        $searchTerm = $this->getRequest()->request->get('term', '');
        $this->term = ($charset === 'utf-8') ? \SpoonFilter::htmlspecialchars(
            $searchTerm
        ) : \SpoonFilter::htmlentities($searchTerm);

        // validate
        if ($this->term === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'term-parameter is missing.');
        }
    }
}
