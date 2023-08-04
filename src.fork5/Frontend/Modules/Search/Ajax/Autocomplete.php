<?php

namespace Frontend\Modules\Search\Ajax;

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
        parent::execute();

        $charset = $this->getContainer()->getParameter('kernel.charset');
        $searchTerm = $this->getRequest()->request->get('term', '');
        $term = ($charset === 'utf-8')
            ? \SpoonFilter::htmlspecialchars($searchTerm) : \SpoonFilter::htmlentities($searchTerm);
        $limit = (int) $this->get('fork.settings')->get('Search', 'autocomplete_num_items', 10);

        if ($term === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'term-parameter is missing.');

            return;
        }

        $url = FrontendNavigation::getUrlForBlock('Search');
        $this->output(
            Response::HTTP_OK,
            array_map(
                function (array $match) use ($url) {
                    $match['url'] = $url . '?form=search&q=' . $match['term'];

                    return $match;
                },
                FrontendSearchModel::getStartsWith($term, LANGUAGE, $limit)
            )
        );
    }
}
