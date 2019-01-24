<?php

namespace Frontend\Modules\Blog\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with that shows all blog articles that share tags with the current page
 */
class RelatedArticles extends Widget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->template->assign('widgetBlogRelatedArticles', $this->getRelatedArticles());
    }

    private function getRelatedArticles(): array
    {
        $relatedIds = FrontendTagsModel::getRelatedItemsByTags(
            $this->get('page')->getId(),
            'Pages',
            $this->getModule()
        );

        if (empty($relatedIds)) {
            return [];
        }

        return FrontendBlogModel::getForTags($relatedIds);
    }
}
