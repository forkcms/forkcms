<?php

namespace Backend\Modules\Tags\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the tags module
 */
class Installer extends ModuleInstaller
{
    /** @var int */
    private $tagsBlockId;

    public function install(): void
    {
        $this->addModule('Tags');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'tags/index', ['tags/edit']);
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Autocomplete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'MassAction');
    }

    private function configureFrontendExtras(): void
    {
        $this->tagsBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), $this->getModule());
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'TagCloud', 'TagCloud');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Related', 'Related');
    }

    private function configureFrontendPages(): void
    {
        $searchId = $this->getSearchWidgetId();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            if ($this->hasPageWithTagsBlock($language)) {
                continue;
            }

            // insert contact page
            $this->insertPage(
                [
                    'title' => $this->getModule(),
                    'type' => 'root',
                    'language' => $language,
                ],
                null,
                ['extra_id' => $this->tagsBlockId, 'position' => 'main'],
                ['extra_id' => $searchId, 'position' => 'top']
            );
        }
    }

    private function getSearchWidgetId(): int
    {
        // @todo: Replace with a ModuleExtraRepository method when it exists.
        return (int) $this->getDatabase()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            ['Search', ModuleExtraType::widget(), 'Form']
        );
    }

    private function hasPageWithTagsBlock(string $language): bool
    {
        // @todo: Replace with a PageRepository method when it exists.
        return (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM pages AS p
             INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
             WHERE b.extra_id = ? AND p.language = ?
             LIMIT 1',
            [$this->tagsBlockId, $language]
        );
    }
}
