<?php

namespace Frontend\Modules\Tags\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

/**
 * This is a widget with the tags
 */
class TagCloud extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        // get categories
        $tags = FrontendTagsModel::getAll();

        // we just need the 10 first items
        $tags = array_slice($tags, 0, 10);

        // build link
        $link = FrontendNavigation::getUrlForBlock('Tags', 'Detail');

        // any tags?
        if (!empty($tags)) {
            // loop and reset url
            foreach ($tags as &$row) {
                $row['url'] = $link . '/' . $row['url'];
            }
        }

        // assign comments
        $this->template->assign('widgetTagsTagCloud', $tags);
    }
}
