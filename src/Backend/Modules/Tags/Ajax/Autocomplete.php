<?php

namespace Backend\Modules\Tags\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the autocomplete-action, it will output a list of tags that start
 * with a certain string.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Autocomplete extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $term = \SpoonFilter::getPostValue('term', null, '');

        // validate
        if ($term == '') {
            $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');
        } else {
            // get tags
            $tags = BackendTagsModel::getStartsWith($term);

            // @todo: let $this->output() convert doctrine objects to arrays
            $results = array();
            foreach ($tags as $tag) {
                $results[] = array(
                    'name' => $tag->getName(),
                    'value' => $tag->getName()
                );
            }

            // output
            $this->output(self::OK, $results);
        }
    }
}
