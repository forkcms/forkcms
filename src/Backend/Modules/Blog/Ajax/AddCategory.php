<?php

namespace Backend\Modules\Blog\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This add-action will create a new category using Ajax
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class AddCategory extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $categoryTitle = trim(\SpoonFilter::getPostValue('value', null, '', 'string'));

        // validate
        if ($categoryTitle === '') {
            $this->output(self::BAD_REQUEST, null, BL::err('TitleIsRequired'));
        } else {
            // get the data
            // build array
            $item['title'] = \SpoonFilter::htmlspecialchars($categoryTitle);
            $item['language'] = BL::getWorkingLanguage();

            $meta['keywords'] = $item['title'];
            $meta['keywords_overwrite'] = 'N';
            $meta['description'] = $item['title'];
            $meta['description_overwrite'] = 'N';
            $meta['title'] = $item['title'];
            $meta['title_overwrite'] = 'N';
            $meta['url'] = BackendBlogModel::getURLForCategory(\SpoonFilter::urlise($item['title']));

            // update
            $item['id'] = BackendBlogModel::insertCategory($item, $meta);

            // output
            $this->output(self::OK, $item, vsprintf(BL::msg('AddedCategory'), array($item['title'])));
        }
    }
}
