<?php

namespace ForkCMS\Modules\Blog\Installer;

use ForkCMS\Modules\Blog\Backend\Actions\BlogIndex;
use ForkCMS\Modules\Blog\Backend\Actions\BlogPostAdd;
use ForkCMS\Modules\Blog\Backend\Actions\CategoryAdd;
use ForkCMS\Modules\Blog\Backend\Actions\CategoryEdit;
use ForkCMS\Modules\Blog\Backend\Actions\CategoryIndex;
use ForkCMS\Modules\Blog\Domain\BlogPost\BlogPost;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Domain\Comment\Comment;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class BlogInstaller extends ModuleInstaller
{
    public function preInstall(): void
    {
        $this->createTableForEntities(
            BlogPost::class,
            Category::class,
            Comment::class
        );
    }

    public function install(): void
    {
        $this->createBackendPages();
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
}
