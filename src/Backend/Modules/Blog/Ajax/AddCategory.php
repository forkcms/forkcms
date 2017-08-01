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
use Symfony\Component\HttpFoundation\Response;

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
            $this->output(Response::HTTP_BAD_REQUEST, null, BL::err('TitleIsRequired'));

            return;
        }

        // get the data
        // build array
        $item = [
            'title' => \SpoonFilter::htmlspecialchars($categoryTitle),
            'language' => BL::getWorkingLanguage(),
        ];

        $meta = [
            'keywords' => $item['title'],
            'keywords_overwrite' => false,
            'description' => $item['title'],
            'description_overwrite' => false,
            'title' => $item['title'],
            'title_overwrite' => false,
            'url' => BackendBlogModel::getUrlForCategory(\SpoonFilter::urlise($item['title'])),
        ];

        // update
        $item['id'] = BackendBlogModel::insertCategory($item, $meta);

        // output
        $this->output(Response::HTTP_OK, $item, vsprintf(BL::msg('AddedCategory'), [$item['title']]));
    }
}
