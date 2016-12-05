<?php

namespace Frontend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is the category-action
 */
class Category extends FrontendBaseBlock
{
    /**
     * @var array
     */
    private $questions;

    /**
     * @var array
     */
    private $record;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $this->tpl->assignGlobal('hideContentTitle', true);
        $this->getData();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // validate incoming parameters
        if ($this->URL->getParameter(1) === null) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // get by URL
        $this->record = FrontendFaqModel::getCategory($this->URL->getParameter(1));

        // anything found?
        if (empty($this->record)) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        $this->record['full_url'] = FrontendNavigation::getURLForBlock('Faq', 'Category') . '/' . $this->record['url'];
        $this->questions = FrontendFaqModel::getAllForCategory($this->record['id']);
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->breadcrumb->addElement($this->record['title']);
        $this->header->setPageTitle($this->record['title']);

        // assign category and questions
        $this->tpl->assign('category', $this->record);
        $this->tpl->assign('questions', $this->questions);
    }
}
