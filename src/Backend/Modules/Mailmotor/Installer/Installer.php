<?php

namespace Backend\Modules\Mailmotor\Installer;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the Mailmotor module
 */
class Installer extends ModuleInstaller
{
    /** @var int */
    private $subscribeBlockId;

    /** @var int */
    private $unsubscribeBlockId;

    public function install(): void
    {
        $this->addModule('Mailmotor');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'mailmotor/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Ping');
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function configureFrontendExtras(): void
    {
        $this->subscribeBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'SubscribeForm', 'Subscribe');
        $this->unsubscribeBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'UnsubscribeForm', 'Unsubscribe');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'SubscribeForm', 'Subscribe');
    }

    private function configureFrontendPages(): void
    {
        // loop languages
        foreach ($this->getLanguages() as $language) {
            $pageId = $this->getPageWithMailmotorBlock($language);
            if ($pageId === null) {
                $pageId = $this->insertPage(
                    ['title' => 'Newsletters', 'language' => $language]
                );
            }

            if (!$this->hasPageWithSubscribeBlock($language)) {
                $this->insertPage(
                    ['parent_id' => $pageId, 'title' => 'Subscribe', 'language' => $language],
                    null,
                    ['extra_id' => $this->subscribeBlockId, 'position' => 'main']
                );
            }

            if (!$this->hasPageWithUnsubscribeBlock($language)) {
                $this->insertPage(
                    ['parent_id' => $pageId, 'title' => 'Unsubscribe', 'language' => $language],
                    null,
                    ['extra_id' => $this->unsubscribeBlockId, 'position' => 'main']
                );
            }
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'api_key', null);
        $this->setSetting($this->getModule(), 'automatically_subscribe_from_form_builder_submitted_form', false);
        $this->setSetting($this->getModule(), 'double_opt_in', true);
        $this->setSetting($this->getModule(), 'list_id', null);
        $this->setSetting($this->getModule(), 'mail_engine', null);
        $this->setSetting($this->getModule(), 'overwrite_interests', false);
    }

    private function hasPageWithSubscribeBlock(string $language): bool
    {
        // @todo: Replace with PageRepository method when it exists.
        return (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM pages AS p
             INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
             WHERE b.extra_id = ? AND p.language = ?
             LIMIT 1',
            [$this->subscribeBlockId, $language]
        );
    }

    private function hasPageWithUnsubscribeBlock(string $language): bool
    {
        // @todo: Replace with PageRepository method when it exists.
        return (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM pages AS p
             INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
             WHERE b.extra_id = ? AND p.language = ?
             LIMIT 1',
            [$this->unsubscribeBlockId, $language]
        );
    }

    private function getPageWithMailmotorBlock(string $language): ?int
    {
        // @todo: Replace with PageRepository method when it exists.
        $pageId = (int) $this->getDatabase()->getVar(
            'SELECT p.id
             FROM pages AS p
             WHERE p.title = ? AND p.language = ?
             LIMIT 1',
            ['Newsletters', $language]
        );

        if ($pageId === 0) {
            return null;
        }

        return $pageId;
    }
}
