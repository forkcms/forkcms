<?php

namespace Frontend\Modules\Blog\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with the link to the archive
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Archive extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // we will cache this widget for 24 hours
        $cacheName = $this->get('current_site')->getId()
            . '_' . FRONTEND_LANGUAGE
            . '_' . $this->getModule()
            . '_' . 'WidgetArchiveCache'
        ;
        $this->tpl->assign('cacheName', $cacheName);
        $this->tpl->cache($cacheName, (24 * 60 * 60));

        // if the widget isn't cached, assign the variables
        if (!$this->tpl->isCached($cacheName)) {
            $this->tpl->assign('widgetBlogArchive', FrontendBlogModel::getArchiveNumbers());
        }
    }
}
