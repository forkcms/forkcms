<?php

namespace Frontend\Modules\Blog\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with that shows all blog articles that share tags with the current page or blog post
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
        // Get the related blog posts based on the page tags
        $relatedIds = FrontendTagsModel::getRelatedItemsByTags(
            $this->get('page')->getId(),
            'Pages',
            $this->getModule()
        );

        if ($this->currentPageIsBlogDetail()) {
            // add the blog post tags as well when we are on the blog detail page
            $relatedIds = array_unique(
                array_merge(
                    $relatedIds,
                    FrontendTagsModel::getRelatedItemsByTags(
                        FrontendBlogModel::getIdForTags($this->url),
                        $this->getModule(),
                        $this->getModule()
                    )
                )
            );
        }

        if (empty($relatedIds)) {
            return [];
        }

        return FrontendBlogModel::getForTags($relatedIds);
    }

    private function currentPageIsBlogDetail(): bool
    {
        $blogDetailUrl = Navigation::getUrlForBlock($this->getModule(), 'Detail');

        return strpos($this->getRequest()->getPathInfo(), $blogDetailUrl) === 0;
    }
}
