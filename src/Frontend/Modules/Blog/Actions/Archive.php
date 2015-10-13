<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the archive-action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Archive extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var    array
     */
    private $items;

    /**
     * The dates for the archive
     *
     * @var    int
     */
    private $startDate;

    /**
     * The dates for the archive
     *
     * @var    int
     */
    private $endDate;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization
     *
     * @var    array
     */
    protected $pagination = array(
        'limit' => 10,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null
    );

    /**
     * The requested year
     *
     * @var    int
     */
    private $year;

    /**
     * The requested month
     *
     * @var    int
     */
    private $month;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();

        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // get parameters
        $this->year = $this->URL->getParameter(1);
        $this->month = $this->URL->getParameter(2);

        // redirect /2010/6 to /2010/06 to avoid duplicate content
        if ($this->month !== null && mb_strlen($this->month) != 2) {
            $queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
            $parameters = array(
                'year' => $this->year,
                'month' => str_pad(
                    $this->month,
                    2,
                    '0',
                    STR_PAD_LEFT
                )
            );
            $this->redirect(
                FrontendNavigation::getURLForBlock('Blog', 'Archive', null, $parameters) . $queryString,
                301
            );
        }
        if (mb_strlen($this->year) != 4) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // redefine
        $this->year = (int) $this->year;
        if ($this->month !== null) {
            $this->month = (int) $this->month;
        }

        // validate parameters
        if ($this->year == 0 || $this->month === 0) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        // rebuild url
        $parameters = array('year' => $this->year);

        // build timestamp
        if ($this->month !== null) {
            $this->startDate = gmmktime(00, 00, 00, $this->month, 01, $this->year);
            $this->endDate = gmmktime(23, 59, 59, $this->month, gmdate('t', $this->startDate), $this->year);
            $parameters['month'] = str_pad($this->month, 2, '0', STR_PAD_LEFT);
        } else {
            // year
            $this->startDate = gmmktime(00, 00, 00, 01, 01, $this->year);
            $this->endDate = gmmktime(23, 59, 59, 12, 31, $this->year);
        }

        // set URL and limit
        $this->pagination['url'] = FrontendNavigation::getURLForBlock('Blog', 'Archive', null, $parameters);
        $this->pagination['limit'] = $this->get('fork.settings')->get('Blog', 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendBlogModel::getAllForDateRangeCount($this->startDate, $this->endDate);
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // redirect if the request page doesn't exists
        if ($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get articles
        $this->items = FrontendBlogModel::getAllForDateRange(
            $this->startDate,
            $this->endDate,
            $this->pagination['limit'],
            $this->pagination['offset']
        );
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // get RSS-link
        $rssTitle = $this->get('fork.settings')->get('Blog', 'rss_title_' . FRONTEND_LANGUAGE);
        $rssLink = FrontendNavigation::getURLForBlock('Blog', 'Rss');

        // add RSS-feed
        $this->header->addRssLink($rssTitle, $rssLink);

        // add into breadcrumb
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::lbl('Archive')));
        $this->breadcrumb->addElement($this->year);
        if ($this->month !== null) {
            $this->breadcrumb->addElement(
                \SpoonDate::getDate('F', $this->startDate, FRONTEND_LANGUAGE, true)
            );
        }

        // set pageTitle
        $this->header->setPageTitle(\SpoonFilter::ucfirst(FL::lbl('Archive')));
        $this->header->setPageTitle($this->year);
        if ($this->month !== null) {
            $this->header->setPageTitle(
                \SpoonDate::getDate('F', $this->startDate, FRONTEND_LANGUAGE, true)
            );
        }

        // assign category
        $this->tpl->assign(
            'archive',
            array(
                 'start_date' => $this->startDate,
                 'end_date' => $this->endDate,
                 'year' => $this->year,
                 'month' => $this->month
            )
        );

        // assign items
        $this->tpl->assign('items', $this->items);

        // assign allowComments
        $this->tpl->assign('allowComments', $this->get('fork.settings')->get('Blog', 'allow_comments'));

        // parse the pagination
        $this->parsePagination();
    }
}
