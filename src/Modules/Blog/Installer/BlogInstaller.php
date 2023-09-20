<?php

namespace ForkCMS\Modules\Blog\Installer;

use ForkCMS\Modules\Blog\Backend\Actions\BlogIndex;
use ForkCMS\Modules\Blog\Backend\Actions\BlogPostAdd;
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
        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Blog'),
            slug: BlogIndex::getActionSlug(),
            selectedFor: [
                BlogPostAdd::getActionSlug(),
            ],
            sequence: 1,
        );
    }
}
