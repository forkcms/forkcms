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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Category extends FrontendBaseBlock
{
    /**
     * @var array
     */
    private $questions;

    /**
     * @var array
     */
    private $category;

    public function execute(): void
    {
        parent::execute();

        $this->template->assignGlobal('hideContentTitle', true);
        $this->getData();
        $this->loadTemplate();
        $this->parse();
    }

    private function getCategory(): array
    {
        if ($this->url->getParameter(1) === null) {
            throw new NotFoundHttpException();
        }

        $category = FrontendFaqModel::getCategory($this->url->getParameter(1));

        if (empty($category)) {
            throw new NotFoundHttpException();
        }

        $baseUrl = FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction());
        $category['full_url'] = $baseUrl . '/' . $category['url'];

        return $category;
    }

    private function getData(): void
    {
        $this->category = $this->getCategory();
        $this->questions = FrontendFaqModel::getAllForCategory($this->category['id']);
    }

    private function parse(): void
    {
        $this->breadcrumb->addElement($this->category['title']);
        $this->header->setPageTitle($this->category['title']);

        $this->template->assign('category', $this->category);
        $this->template->assign('questions', $this->questions);
    }
}
