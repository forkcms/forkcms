<?php

namespace Backend\Modules\Blog\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This add-action will create a new category using Ajax
 */
class AddCategory extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $categoryTitle = trim($this->getRequest()->request->get('value', ''));

        // validate
        if ($categoryTitle === '') {
            $this->output(self::BAD_REQUEST, null, BL::err('TitleIsRequired'));
        } else {
            // get the data
            // build array
            $item = [
                'title' => \SpoonFilter::htmlspecialchars($categoryTitle),
                'language' => BL::getWorkingLanguage(),
            ];

            $meta = [
                'keywords' => $item['title'],
                'keywords_overwrite' => 'N',
                'description' => $item['title'],
                'description_overwrite' => 'N',
                'title' => $item['title'],
                'title_overwrite' => 'N',
                'url' => BackendBlogModel::getURLForCategory(\SpoonFilter::urlise($item['title'])),
            ];

            // update
            $item['id'] = BackendBlogModel::insertCategory($item, $meta);

            // output
            $this->output(self::OK, $item, vsprintf(BL::msg('AddedCategory'), [$item['title']]));
        }
    }
}
