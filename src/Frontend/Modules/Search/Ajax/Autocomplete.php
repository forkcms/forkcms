<?php

namespace Frontend\Modules\Search\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;

/**
 * This is the autocomplete-action, it will output a list of searches that start with a certain string.
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Autocomplete extends FrontendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $searchTerm = \SpoonFilter::getPostValue('term', null, '');
        $term = (SPOON_CHARSET == 'utf-8') ? \SpoonFilter::htmlspecialchars($searchTerm) : \SpoonFilter::htmlentities(
            $searchTerm
        );
        $limit = (int) FrontendModel::getModuleSetting('Search', 'autocomplete_num_items', 10);

        // validate
        if ($term == '') {
            $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');
        } else {
            // get matches
            $matches = FrontendSearchModel::getStartsWith($term, FRONTEND_LANGUAGE, $limit);

            // get search url
            $url = FrontendNavigation::getURLForBlock('Search');

            // loop items and set search url
            foreach ($matches as &$match) {
                $match['url'] = $url . '?form=search&q=' . $match['term'];
            }

            // output
            $this->output(self::OK, $matches);
        }
    }
}
