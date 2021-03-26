<?php

namespace Backend\Modules\Blog\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Blog\Domain\Category\Category;
use Backend\Modules\Blog\Domain\Category\CategoryRepository;
use Backend\Modules\Blog\Domain\Comment\Comment;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Common\BlockEditor\EditorBlocks;
use ForkCMS\Bundle\InstallerBundle\Language\Locale;
use Backend\Modules\Pages\Domain\Page\PageRepository;

/**
 * Installer for the blog module
 */
class Installer extends ModuleInstaller
{
    /** @var int - Default category id */
    private $defaultCategoryId;

    /** @var int */
    private $blogBlockId;

    public function install(): void
    {
        $this->addModule('Blog');
        $this->makeSearchable($this->getModule());
        $this->configureEntities();
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureBackendWidgets();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureEntities(): void
    {
        Model::get('fork.entity.create_schema')->forEntityClasses(
            [
                Comment::class,
                Category::class,
            ]
        );
    }

    private function configureBackendActionRightsForBlogArticle(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'ImportWordpress');
        $this->setActionRights(1, $this->getModule(), 'Index');
    }

    private function configureBackendActionRightsForBlogArticleComment(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Comments');
        $this->setActionRights(1, $this->getModule(), 'DeleteSpam');
        $this->setActionRights(1, $this->getModule(), 'EditComment');
        $this->setActionRights(1, $this->getModule(), 'MassCommentAction');
    }

    private function configureBackendActionRightsForBlogCategory(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationBlogId = $this->setNavigation($navigationModulesId, 'Blog');
        $this->setNavigation(
            $navigationBlogId,
            'Articles',
            'blog/index',
            ['blog/add', 'blog/edit', 'blog/import_wordpress']
        );
        $this->setNavigation($navigationBlogId, 'Comments', 'blog/comments', ['blog/edit_comment']);
        $this->setNavigation(
            $navigationBlogId,
            'Categories',
            'blog/categories',
            ['blog/add_category', 'blog/edit_category']
        );

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Blog', 'blog/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        // Action rights for entities
        $this->configureBackendActionRightsForBlogArticle();
        $this->configureBackendActionRightsForBlogCategory();
        $this->configureBackendActionRightsForBlogArticleComment();

        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function configureBackendWidgets(): void
    {
        $this->insertDashboardWidget('Blog', 'Comments');
    }

    private function configureFrontendExtras(): void
    {
        $this->blogBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'Blog');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Archive', 'Archive');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Categories', 'Categories');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RecentArticlesFull', 'RecentArticlesFull');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RecentArticlesList', 'RecentArticlesList');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RecentComments', 'RecentComments');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RelatedArticles', 'RelatedArticles');
    }

    private function configureFrontendPages(): void
    {
        // get search extra id
        $searchId = $this->getSearchWidgetId();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // fetch current categoryId
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId === 0) {
                // add category
                $this->defaultCategoryId = $this->insertCategory($language, 'Default', 'default');
            }

            // RSS settings
            $this->setSetting($this->getModule(), 'rss_meta_' . $language, true);
            $this->setSetting($this->getModule(), 'rss_title_' . $language, 'RSS');
            $this->setSetting($this->getModule(), 'rss_description_' . $language, '');

            // check if a page for blog already exists in this language
            if (!$this->hasPageWithBlogBlock($language)) {
                $this->insertPage(
                    ['title' => 'Blog', 'language' => $language],
                    null,
                    ['extra_id' => $this->blogBlockId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );
            }

            if ($this->installExample()) {
                $this->installExampleData($language);
            }
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'allow_comments', true);
        $this->setSetting($this->getModule(), 'max_num_revisions', 20);
        $this->setSetting($this->getModule(), 'moderation', true);
        $this->setSetting($this->getModule(), 'overview_num_items', 10);
        $this->setSetting($this->getModule(), 'recent_articles_full_num_items', 3);
        $this->setSetting($this->getModule(), 'recent_articles_list_num_items', 5);
        $this->setSetting($this->getModule(), 'requires_akismet', true);
        $this->setSetting($this->getModule(), 'spamfilter', false);
    }

    private function getCategory(string $language): int
    {
        $category = Model::get(CategoryRepository::class)->findOneByLocale($language);

        if (!$category instanceof Category) {
            return 0;
        }

        return $category->getId();
    }

    private function getSearchWidgetId(): int
    {
        /** @var ModuleExtraRepository $moduleExtraRepository */
        $moduleExtraRepository = Model::get(ModuleExtraRepository::class);
        $widgetId = $moduleExtraRepository->getModuleExtraId('Search', 'Form', ModuleExtraType::widget());

        if ($widgetId === null) {
            throw new \RuntimeException('Could not find Search Widget');
        }

        return $widgetId;
    }

    private function hasPageWithBlogBlock(string $language): bool
    {
        return Model::getContainer()->get(PageBlockRepository::class)->moduleExtraExistsForLocale(
            $this->blogBlockId,
            Locale::fromString(
                $language
            )
        );
    }

    /**
     * @todo: Replace this with a BlogCategoryRepository method when it exists.
     */
    private function insertCategory(string $language, string $title, string $url): int
    {
        $item = [];
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['locale'] = $language;
        $item['title'] = $title;

        return (int) $this->getDatabase()->insert('blog_categories', $item);
    }

    private function getNextBlogPostId(): int
    {
        return 1 + (int) $this->getDatabase()->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1');
    }

    /**
     * @todo: this method should be rewritten to use DataFixtures.
     */
    private function installExampleData(string $language): void
    {
        // get database instance
        $database = $this->getDatabase();

        // check if blogposts already exist in this language
        if (!(bool) $database->getVar(
            'SELECT 1
             FROM blog_posts
             WHERE language = ?
             LIMIT 1',
            [$language]
        )
        ) {
            // insert sample blogpost 1
            $firstBlogPostId = $this->getNextBlogPostId();
            $database->insert(
                'blog_posts',
                [
                    'id' => $firstBlogPostId,
                    'category_id' => $this->defaultCategoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta(
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'nunc-sediam-est'
                    ),
                    'language' => $language,
                    'title' => 'Nunc sediam est',
                    'introduction' => EditorBlocks::createJsonFromHtml(
                        file_get_contents(__DIR__ . '/Data/' . $language . '/sample1.txt')
                    ),
                    'text' => EditorBlocks::createJsonFromHtml(
                        file_get_contents(__DIR__ . '/Data/' . $language . '/sample1.txt')
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00'),
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'edited_on' => gmdate('Y-m-d H:i:00'),
                    'hidden' => false,
                    'allow_comments' => true,
                    'num_comments' => '2',
                ]
            );

            // add Search index blogpost 1
            $this->addSearchIndex(
                $this->getModule(),
                $firstBlogPostId,
                [
                    'title' => 'Nunc sediam est',
                    'text' => EditorBlocks::createJsonFromHtml(
                        file_get_contents(__DIR__ . '/Data/' . $language . '/sample1.txt')
                    ),
                ],
                $language
            );

            // insert sample blogpost 2
            $secondBlogPostId = $this->getNextBlogPostId();
            $database->insert(
                'blog_posts',
                [
                    'id' => $secondBlogPostId,
                    'category_id' => $this->defaultCategoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
                    'language' => $language,
                    'title' => 'Lorem ipsum',
                    'introduction' => EditorBlocks::createJsonFromHtml(
                        file_get_contents(__DIR__ . '/Data/' . $language . '/sample1.txt')
                    ),
                    'text' => EditorBlocks::createJsonFromHtml(
                        file_get_contents(__DIR__ . '/Data/' . $language . '/sample1.txt')
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'hidden' => false,
                    'allow_comments' => true,
                    'num_comments' => '0',
                ]
            );

            // add Search index blogpost 2
            $this->addSearchIndex(
                $this->getModule(),
                $secondBlogPostId,
                [
                    'title' => 'Lorem ipsum',
                    'text' => EditorBlocks::createJsonFromHtml(
                        file_get_contents(__DIR__ . '/Data/' . $language . '/sample1.txt')
                    ),
                ],
                $language
            );

            // insert example comment 1
            $database->insert(
                'blog_comments',
                [
                    'postId' => $firstBlogPostId,
                    'locale' => $language,
                    'createdOn' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Davy Hellemans',
                    'email' => 'forkcms-sample@spoon-library.com',
                    'website' => 'http://www.spoon-library.com',
                    'text' => 'awesome!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null,
                ]
            );

            // insert example comment 2
            $database->insert(
                'blog_comments',
                [
                    'postId' => $firstBlogPostId,
                    'locale' => $language,
                    'createdOn' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Tijs Verkoyen',
                    'email' => 'forkcms-sample@sumocoders.be',
                    'website' => 'https://www.sumocoders.be',
                    'text' => 'wicked!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null,
                ]
            );
        }
    }
}
