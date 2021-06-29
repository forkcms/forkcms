<?php

namespace Backend\Modules\ContentBlocks\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Common\Doctrine\Entity\CreateSchema;

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
            'content_blocks/index',
            ['content_blocks/add', 'content_blocks/edit']
        );
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
    }

    private function configureEntities(): void
    {
        Model::get(CreateSchema::class)->forEntityClass(ContentBlock::class);
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'max_num_revisions', 20);
    }
}
