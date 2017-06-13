<?php

namespace Backend\Modules\ContentBlocks\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;

/**
 * Installer for the content blocks module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('ContentBlocks');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureEntities();
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            $this->getModule(),
            'content_blocks/content_block_index',
            ['content_blocks/content_block_add', 'content_blocks/content_block_edit']
        );
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'ContentBlockAdd');
        $this->setActionRights(1, $this->getModule(), 'ContentBlockDelete');
        $this->setActionRights(1, $this->getModule(), 'ContentBlockEdit');
        $this->setActionRights(1, $this->getModule(), 'ContentBlockIndex');
    }

    private function configureEntities(): void
    {
        Model::get('fork.entity.create_schema')->forEntityClass(ContentBlock::class);
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'max_num_revisions', 20);
    }
}
