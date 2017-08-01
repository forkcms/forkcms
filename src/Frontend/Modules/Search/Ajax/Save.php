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
use Frontend\Modules\Search\Engine\Model as FrontendSearchModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the save-action, it will save the searched term in the statistics
 */
class Save extends FrontendBaseAJAXAction
{
    /**
     * @var array
     */
    private $statistics;

    public function execute(): void
    {
        parent::execute();

        // get parameters
        $charset = $this->getContainer()->getParameter('kernel.charset');
        $searchTerm = $this->getRequest()->request->get('term', '');
        $term = ($charset == 'utf-8') ? \SpoonFilter::htmlspecialchars($searchTerm) : \SpoonFilter::htmlentities(
            $searchTerm
        );

        // validate search term
        if ($term === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'term-parameter is missing.');

            return;
        }
        // previous search result
        $previousTerm = FrontendModel::getSession()->get('searchTerm', '');
        FrontendModel::getSession()->set('searchTerm', '');

        // save this term?
        if ($previousTerm !== $term) {
            // format data
            $this->statistics = [];
            $this->statistics['term'] = $term;
            $this->statistics['language'] = LANGUAGE;
            $this->statistics['time'] = FrontendModel::getUTCDate();
            $this->statistics['data'] = serialize(['server' => $_SERVER]);
            $this->statistics['num_results'] = FrontendSearchModel::getTotal($term);

            // save data
            FrontendSearchModel::save($this->statistics);
        }

        // save current search term in cookie
        FrontendModel::getSession()->set('searchTerm', $term);

        // output
        $this->output(Response::HTTP_OK);
    }
}
