<?php

namespace ForkCMS\Modules\Blog\Installer;

use ForkCMS\Modules\Blog\Backend\Actions\BlogIndex;
use ForkCMS\Modules\Blog\Backend\Actions\BlogPostAdd;
use ForkCMS\Modules\Blog\Backend\Actions\BlogPostEdit;
use ForkCMS\Modules\Blog\Backend\Actions\CategoryAdd;
use ForkCMS\Modules\Blog\Backend\Actions\CategoryEdit;
use ForkCMS\Modules\Blog\Backend\Actions\CategoryIndex;
use ForkCMS\Modules\Blog\Backend\Actions\ModuleSettings;
use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Domain\Comment\Comment;
use ForkCMS\Modules\Blog\Frontend\Actions\Detail;
use ForkCMS\Modules\Blog\Frontend\Actions\Index;
use ForkCMS\Modules\Blog\Frontend\Actions\Category as CategoryAction;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class BlogInstaller extends ModuleInstaller
{
    public function preInstall(): void
    {
        $this->createTableForEntities(
            Article::class,
            Category::class,
            Comment::class
        );
    }

    public function install(): void
    {
        $this->createBackendPages();
        $this->createSettings();
        $this->createFrontendPages();
    }

    private function createFrontendPages(): void
    {
        $this->getOrCreateFrontendBlock(Index::getModuleBlock()->getName());
        $this->getOrCreateFrontendBlock(Detail::getModuleBlock()->getName());
        $this->getOrCreateFrontendBlock(CategoryAction::getModuleBlock()->getName());
    }

    private function createBackendPages(): void
    {
        $modulesNavigationItem = $this->getModulesNavigationItem();
        $blogNavigationitem = $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Blog'),
            parent: $modulesNavigationItem
        );

        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Articles'),
            slug: BlogIndex::getActionSlug(),
            parent: $blogNavigationitem,
            selectedFor: [
                BlogPostAdd::getActionSlug(),
                BlogPostEdit::getActionSlug(),
            ],
            sequence: 1,
        );

        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Categories'),
            slug: CategoryIndex::getActionSlug(),
            parent: $blogNavigationitem,
            selectedFor: [
                CategoryAdd::getActionSlug(),
                CategoryEdit::getActionSlug(),
            ],
            sequence: 2,
        );
    }

    private function createSettings(): void
    {
        $moduleSettings = $this->getModuleSettingsNavigationItem();
        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Blog'),
            slug: ModuleSettings::getActionSlug(),
            parent: $moduleSettings,
            selectedFor: [ModuleSettings::getActionSlug()],
            sequence: 5,
        );
    }
}
