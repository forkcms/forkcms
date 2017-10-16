<?php

namespace Frontend\Modules\Faq\Actions;

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

        $category['full_url'] = FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction())
                                . '/' . $category['url'];

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
