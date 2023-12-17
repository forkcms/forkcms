<?php

namespace ForkCMS\Modules\ContentBlocks\Installer;

use ForkCMS\Modules\ContentBlocks\Backend\Actions\ContentBlockAdd;
use ForkCMS\Modules\ContentBlocks\Backend\Actions\ContentBlockDelete;
use ForkCMS\Modules\ContentBlocks\Backend\Actions\ContentBlockEdit;
use ForkCMS\Modules\ContentBlocks\Backend\Actions\ContentBlockIndex;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class ContentBlocksInstaller extends ModuleInstaller
{
    public function preInstall(): void
    {
        $this->createTableForEntities(ContentBlock::class);
    }

    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->createBackendPages();
    }

    private function createBackendPages(): void
    {
        $modulesNavigationItem = $this->getModulesNavigationItem();

        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('ContentBlocks'),
            slug: ContentBlockIndex::getActionSlug(),
            parent: $modulesNavigationItem,
            selectedFor: [
                ContentBlockAdd::getActionSlug(),
                ContentBlockEdit::getActionSlug(),
                ContentBlockDelete::getActionSlug(),
            ]
        );
    }
}
