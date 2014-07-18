<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our implementation of iSpoonDataGridPaging
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class DataGridPaging implements \iSpoonDataGridPaging
{
    /**
     * Builds & returns the pagination
     *
     * @param string $URL
     * @param int    $offset
     * @param string $order      The name of the column to sort on.
     * @param string $sort       The sorting method, possible values are: asc, desc.
     * @param int    $numResults
     * @param int    $numPerPage The items per page.
     * @param bool   $debug
     * @param string $compileDirectory
     * @return string
     */
    public static function getContent(
        $URL,
        $offset,
        $order,
        $sort,
        $numResults,
        $numPerPage,
        $debug = true,
        $compileDirectory = null
    ) {
        // if there is just one page we don't need paging
        if ($numResults < $numPerPage) {
            return '';
        }

        // load template
        $tpl = new \SpoonTemplate();

        // compile directory
        if ($compileDirectory !== null) {
            $tpl->setCompileDirectory($compileDirectory);
        } else {
            $tpl->setCompileDirectory(dirname(__FILE__));
        }

        // force compiling
        $tpl->setForceCompile((bool) $debug);

        // init vars
        $pagination = null;
        $showFirstPages = false;
        $showLastPages = false;

        // current page
        $currentPage = ceil($offset / $numPerPage) + 1;

        // number of pages
        $numPages = ceil($numResults / $numPerPage);

        // populate count fields
        $pagination['num_pages'] = $numPages;
        $pagination['current_page'] = $currentPage;

        // as long as we have more then 5 pages and are 5 pages from the end we should show all pages till the end
        if ($currentPage > 5 && $currentPage >= ($numPages - 4)) {
            // init vars
            $pagesStart = ($numPages > 7) ? $numPages - 5 : $numPages - 6;
            $pagesEnd = $numPages;

            // fix for page 6
            if ($numPages == 6) {
                $pagesStart = 1;
            }

            // show first pages
            if ($numPages > 7) {
                $showFirstPages = true;
            }
        } elseif ($currentPage <= 5) {
            // as long as we are below page 5 and below 5 from the end we should show all pages starting from 1
            $pagesStart = 1;
            $pagesEnd = 6;


            if ($numPages == 7) {
                // when we have 7 pages, show 7 as end
                $pagesEnd = 7;
            } elseif ($numPages <= 6) {
                // when we have less then 6 pages, show the maximum page
                $pagesEnd = $numPages;
            }

            // show last pages
            if ($numPages > 7) {
                $showLastPages = true;
            }
        } else {
            // page 6
            $pagesStart = $currentPage - 2;
            $pagesEnd = $currentPage + 2;
            $showFirstPages = true;
            $showLastPages = true;
        }

        // show previous
        if ($currentPage > 1) {
            // set
            $pagination['show_previous'] = true;
            $pagination['previous_url'] = str_replace(
                array('[offset]', '[order]', '[sort]'),
                array(($offset - $numPerPage), $order, $sort),
                $URL
            );
        }

        // show first pages?
        if ($showFirstPages) {
            // init var
            $pagesFirstStart = 1;
            $pagesFirstEnd = 2;

            // loop pages
            for ($i = $pagesFirstStart; $i <= $pagesFirstEnd; $i++) {
                // add
                $pagination['first'][] = array(
                    'url' => str_replace(
                        array('[offset]', '[order]', '[sort]'),
                        array((($numPerPage * $i) - $numPerPage), $order, $sort),
                        $URL
                    ),
                    'label' => $i
                );
            }
        }

        // build array
        for ($i = $pagesStart; $i <= $pagesEnd; $i++) {
            // init var
            $current = ($i == $currentPage);

            // add
            $pagination['pages'][] = array(
                'url' => str_replace(
                    array('[offset]', '[order]', '[sort]'),
                    array((($numPerPage * $i) - $numPerPage), $order, $sort),
                    $URL
                ),
                'label' => $i,
                'current' => $current
            );
        }

        // show last pages?
        if ($showLastPages) {
            // init var
            $pagesLastStart = $numPages - 1;
            $pagesLastEnd = $numPages;

            // loop pages
            for ($i = $pagesLastStart; $i <= $pagesLastEnd; $i++) {
                // add
                $pagination['last'][] = array(
                    'url' => str_replace(
                        array('[offset]', '[order]', '[sort]'),
                        array((($numPerPage * $i) - $numPerPage), $order, $sort),
                        $URL
                    ),
                    'label' => $i
                );
            }
        }

        // show next
        if ($currentPage < $numPages) {
            // set
            $pagination['show_next'] = true;
            $pagination['next_url'] = str_replace(
                array('[offset]', '[order]', '[sort]'),
                array(($offset + $numPerPage), $order, $sort),
                $URL
            );
        }

        // multiple pages
        $pagination['multiple_pages'] = ($numPages == 1) ? false : true;

        // assign pagination
        $tpl->assign('pagination', $pagination);

        // assign labels
        $tpl->assign('previousLabel', Language::lbl('PreviousPage'));
        $tpl->assign('nextLabel', Language::lbl('NextPage'));
        $tpl->assign('goToLabel', Language::lbl('GoToPage'));

        return $tpl->getContent(BACKEND_CORE_PATH . '/Layout/Templates/DatagridPaging.tpl');
    }
}
