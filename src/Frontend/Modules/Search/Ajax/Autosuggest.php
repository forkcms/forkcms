<?php

namespace Frontend\Modules\Search\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;

/**
 * This is the auto suggest-action, it will output a list of results for a certain search
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Autosuggest extends FrontendBaseAJAXAction
{
    /**
     * Name of the cache file
     *
     * @var    string
     */
    private $cacheFile;

    /**
     * The items
     *
     * @var    array
     */
    private $items;

    /**
     * Limit of data to fetch
     *
     * @var    int
     */
    private $limit;

    /**
     * Offset of data to fetch
     *
     * @var    int
     */
    private $offset;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization.
     *
     * @var    array
     */
    protected $pagination = array(
        'limit' => 20,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null
    );

    /**
     * The requested page
     *
     * @var    int
     */
    private $requestedPage;

    /**
     * The search term
     *
     * @var string
     */
    private $term = '';

    /**
     * Display
     */
    private function display()
    {
        // set variables
        $this->requestedPage = 1;
        $this->limit = (int) FrontendModel::getModuleSetting('Search', 'autosuggest_num_items', 10);
        $this->offset = ($this->requestedPage * $this->limit) - $this->limit;
        $this->cacheFile = FRONTEND_CACHE_PATH . '/' . $this->getModule() . '/' .
                           FRONTEND_LANGUAGE . '_' . md5($this->term) . '_' .
                           $this->offset . '_' . $this->limit . '.php';

        // load the cached data
        if (!$this->getCachedData()) {
            // ... or load the real data
            $this->getRealData();
        }

        // parse
        $this->parse();
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->validateForm();
        $this->display();
    }

    /**
     * Load the cached data
     *
     * @return bool
     */
    private function getCachedData()
    {
        // no search term = no search
        if (!$this->term) {
            return false;
        }

        // debug mode = no cache
        if (SPOON_DEBUG) {
            return false;
        }

        // check if cache file exists
        if (!is_file($this->cacheFile)) {
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

    /**
     * Load the data
     */
    private function getRealData()
    {
        // no search term = no search
        if (!$this->term) {
            return;
        }

        // set url
        $this->pagination['url'] = FrontendNavigation::getURLForBlock('Search') . '?form=search&q=' . $this->term;
        $this->pagination['limit'] = FrontendModel::getModuleSetting('Search', 'overview_num_items', 20);

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $this->requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

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

        // redirect if the request page doesn't exist
        if ($this->requestedPage > $this->pagination['num_pages'] || $this->requestedPage < 1) {
            $this->redirect(
                FrontendNavigation::getURL(404)
            );
        }

        // debug mode = no cache
        if (!SPOON_DEBUG) {
            // set cache content
            $fs = new Filesystem();
            $fs->dumpFile(
                $this->cacheFile,
                "<?php\n" . '$pagination = ' . var_export($this->pagination, true) . ";\n" . '$items = ' . var_export(
                    $this->items,
                    true
                ) . ";\n?>"
            );
        }
    }

    public function parse()
    {
        // more matches to be found than?
        if ($this->pagination['num_items'] > count($this->items)) {
            // remove last result (to add this reference)
            array_pop($this->items);

            // add reference to full search results page
            $this->items[] = array(
                'title' => FL::lbl('More'),
                'text' => FL::msg('MoreResults'),
                'full_url' => FrontendNavigation::getURLForBlock('Search') . '?form=search&q=' . $this->term
            );
        }

        // format data
        foreach ($this->items as &$item) {
            // format description
            $item['text'] = !empty($item['text']) ? (mb_strlen($item['text']) > $this->length ? mb_substr(strip_tags($item['text']), 0, $this->length, SPOON_CHARSET) . '…' : $item['text']) : '';
        }

        // output
        $this->output(self::OK, $this->items);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // set values
        $searchTerm = \SpoonFilter::getPostValue('term', null, '');
        $this->term = (SPOON_CHARSET == 'utf-8') ? \SpoonFilter::htmlspecialchars(
            $searchTerm
        ) : \SpoonFilter::htmlentities($searchTerm);
        $this->length = (int) \SpoonFilter::getPostValue('length', null, 50);

        // validate
        if ($this->term == '') {
            $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');
        }
    }
}
