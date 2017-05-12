<?php

namespace Backend\Modules\Search\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModuleExtraType;
use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the search module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Search');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        $this->setModuleSettings();
        $this->configureModuleRightsForGroup(1);
        $this->addBackendNavigation();
        $this->addModuleExtras();
        $this->addPageSearchIndexes();
    }

    private function addPageSearchIndexes(): void
    {
        $this->makeSearchable('Pages');

        foreach ($this->getActivePages() as $page) {
            $this->addSearchIndexForPage($page['id'], $page['language'], $page['title']);
            $this->addSearchIndexForPage(
                $page['id'],
                $page['language'],
                $this->getContentFromBlocksForPageRevision($page['revision_id'])
            );
        }
    }

    private function setModuleSettings(): void
    {
        $this->setSetting($this->getModule(), 'overview_num_items', 10);
        $this->setSetting($this->getModule(), 'validate_search', true);
    }

    private function configureModuleRightsForGroup(int $groupId): void
    {
        $this->setModuleRights($groupId, $this->getModule());
        $this->setActionRights($groupId, $this->getModule(), 'AddSynonym');
        $this->setActionRights($groupId, $this->getModule(), 'EditSynonym');
        $this->setActionRights($groupId, $this->getModule(), 'DeleteSynonym');
        $this->setActionRights($groupId, $this->getModule(), 'Settings');
        $this->setActionRights($groupId, $this->getModule(), 'Statistics');
        $this->setActionRights($groupId, $this->getModule(), 'Synonyms');
    }

    private function addBackendNavigation(): void
    {
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationSearchId = $this->setNavigation($navigationModulesId, 'Search');
        $this->setNavigation($navigationSearchId, 'Statistics', 'search/statistics');
        $this->setNavigation(
            $navigationSearchId,
            'Synonyms',
            'search/synonyms',
            ['search/add_synonym', 'search/edit_synonym']
        );

        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Search', 'search/settings');
    }

    private function addModuleExtras(): void
    {
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'SearchForm', 'Form', null, false, 2001);
        $searchId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'Search', null, null, false, 2000);
        $this->createSearchIndexPage($searchId);
    }

    private function createSearchIndexPage(int $searchId): void
    {
        foreach ($this->getLanguages() as $language) {
            $searchIndexAlreadyExists = (bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                [$searchId, $language]
            );

            if ($searchIndexAlreadyExists) {
                continue;
            }

            $searchIndexPageTitle = $this->getLocale('Search', 'Core', $language, 'lbl', 'Frontend');
            $this->insertPage(
                [
                    'title' => \SpoonFilter::ucfirst($searchIndexPageTitle),
                    'type' => 'root',
                    'language' => $language,
                ],
                null,
                ['extra_id' => $searchId, 'position' => 'main']
            );
        }
    }

    private function getActivePages(): array
    {
        return (array) $this->getDB()->getRecords(
            'SELECT id, revision_id, language, title
             FROM pages
             WHERE status = ?',
            ['active']
        );
    }

    private function getContentFromBlocksForPageRevision(int $pageRevisionId): string
    {
        $blocks = (array) $this->getDB()->getColumn(
            'SELECT html FROM pages_blocks WHERE revision_id = ?',
            [$pageRevisionId]
        );

        return empty($blocks) ? '' : strip_tags(implode(' ', $blocks));
    }

    private function addSearchIndexForPage(int $id, string $language, string $term): void
    {
        $this->getDB()->execute(
            'INSERT INTO search_index (module, other_id, language, field, value, active)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?, active = ?',
            ['Pages', $id, $language, 'title', $term, 'Y', $term, 'Y']
        );
    }
}
