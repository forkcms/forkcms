<?php

namespace Frontend\Modules\Search\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the autocomplete-action, it will output a list of searches that start with a certain string.
 */
class Autocomplete extends FrontendBaseAJAXAction
{
    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $charset = $this->getContainer()->getParameter('kernel.charset');
        $searchTerm = $this->getRequest()->request->get('term', '');
        $term = ($charset === 'utf-8') ? \SpoonFilter::htmlspecialchars($searchTerm) : \SpoonFilter::htmlentities(
            $searchTerm
        );
        $limit = (int) $this->get('fork.settings')->get('Search', 'autocomplete_num_items', 10);

        // validate
        if ($term === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'term-parameter is missing.');

            return;
        }

        // get matches
        $matches = FrontendSearchModel::getStartsWith($term, LANGUAGE, $limit);

        // get search url
        $url = FrontendNavigation::getUrlForBlock('Search');

        // loop items and set search url
        foreach ($matches as &$match) {
            $match['url'] = $url . '?form=search&q=' . $match['term'];
        }

        // output
        $this->output(Response::HTTP_OK, $matches);
    }
}
