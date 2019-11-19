<?php

namespace Frontend\Modules\Tags\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Language as FL;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Detail extends FrontendBaseBlock
{
    /**
     * The tag
     *
     * @var array
     */
    private $tag = [];

    /**
     * The items per module with this tag
     *
     * @var array
     */
    private $tagsModules = [];

    public function execute(): void
    {
        parent::execute();

        $this->template->assignGlobal('hideContentTitle', true);
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    private function getTag(): array
    {
        if ($this->url->getParameter(1) === null) {
            throw new NotFoundHttpException();
        }

        $tag = FrontendTagsModel::get($this->url->getParameter(1));

        if (empty($tag)) {
            throw new NotFoundHttpException();
        }

        return $tag;
    }

    private function getData(): void
    {
        $this->tag = $this->getTag();
        $this->tagsModules = FrontendTagsModel::getItemsForTag($this->tag['id']);
    }

    private function parse(): void
    {
        $this->header->setPageTitle($this->tag['name']);
        $this->template->assign('tag', $this->tag);
        $this->template->assign('tagsModules', $this->tagsModules);
        $this->breadcrumb->addElement($this->tag['name']);

        // tag-pages don't have any SEO-value, so don't index them
        $this->header->addMetaData(['name' => 'robots', 'content' => 'noindex, follow'], true);
    }
}
