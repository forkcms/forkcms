<?php

namespace Frontend\Modules\Blog\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Rss as FrontendRSS;
use Frontend\Core\Engine\RssItem as FrontendRSSItem;
use Frontend\Core\Engine\User as FrontendUser;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;
use SpoonFilter;

class Rss extends FrontendBaseBlock
{
    public function execute(): void
    {
        parent::execute();

        $this->generateRss();
    }

    private function generateRss()
    {
        $blogPosts = FrontendBlogModel::getAll(30);

        $rss = new FrontendRSS(
            $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE, SITE_DEFAULT_TITLE),
            SITE_URL . FrontendNavigation::getUrlForBlock($this->getModule()),
            $this->get('fork.settings')->get('Blog', 'rss_description_' . LANGUAGE, '')
        );

        foreach ($blogPosts as $blogPost) {
            $rss->addItem(
                $this->getRssFeedItemForBlogPost(
                    $blogPost,
                    $this->get('fork.settings')->get($this->getModule(), 'rss_meta_' . LANGUAGE, true)
                )
            );
        }

        $rss->parse();
    }

    private function getRssFeedItemForBlogPost(array $blogPost, bool $includeMeta): FrontendRSSItem
    {
        $rssItem = new FrontendRSSItem(
            $blogPost['title'],
            $blogPost['full_url'],
            $this->getDescriptionForBlogPost($blogPost, $includeMeta)
        );

        $rssItem->setPublicationDate($blogPost['publish_on']);
        $rssItem->addCategory($blogPost['category_title']);
        $rssItem->setAuthor(FrontendUser::getBackendUser($blogPost['user_id'])->getEmail());

        return $rssItem;
    }

    private function getMetaDescription(
        string $blogPostTitle,
        string $blogPostLink,
        string $blogPostAuthor,
        string $categoryTitle,
        string $categoryLink
    ): string {
        return sprintf(
            '<p><a href="%1$s" title="%2$s">%2$s</a> %3$s %4$s <a href="%5$s" title="%6$s">%6$s</a></p>',
            $blogPostLink,
            $blogPostTitle,
            sprintf(FL::msg('WrittenBy'), $blogPostAuthor),
            FL::lbl('In'),
            $categoryLink,
            $categoryTitle
        );
    }

    private function getDescriptionForBlogPost(array $blogPost, bool $includeMeta): string
    {
        $description = empty($blogPost['introduction']) ? $blogPost['text'] : $blogPost['introduction'];

        if (!$includeMeta) {
            return $description;
        }

        $description .= '<div class="meta">';

        $description .= $this->getMetaDescription(
            $blogPost['title'],
            $blogPost['full_url'],
            FrontendUser::getBackendUser($blogPost['user_id'])->getSetting('nickname'),
            $blogPost['category_title'],
            $blogPost['category_full_url']
        );

        if (isset($blogPost['tags'])) {
            $description .= $this->getTagsDescription($blogPost['tags']);
        }

        return $description;
    }

    private function getTagsDescription(array $tags): string
    {
        if (empty($tags)) {
            return '';
        }

        return sprintf(
            '<p>%1$s: %2$s</p>',
            SpoonFilter::ucfirst(FL::lbl('Tags')),
            implode(
                ', ',
                array_map(
                    function (array $tag) : string {
                        return sprintf(
                            '<a href="%1$s" rel="tag" title="%2$s">%2$s</a>',
                            $tag['full_url'],
                            $tag['name']
                        );
                    },
                    $tags
                )
            )
        );
    }
}
