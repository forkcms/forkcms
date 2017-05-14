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
    public function install(): void
    {
        $this->addModule('Mailmotor');

        // install settings
        $this->installSettings();

        // install locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // install the mailmotor module
        $this->installModuleAndActions();

        // install backend navigation
        $this->installBackendNavigation();

        // install the pages for the module
        $this->installPages();
    }

    private function installModuleAndActions(): void
    {
        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function installBackendNavigation(): void
    {
        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'mailmotor/settings');
    }

    private function installPages(): void
    {
        // add extra's
        $subscribeId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'SubscribeForm',
            'Subscribe',
            null,
            false,
            3001
        );
        $unsubscribeId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'UnsubscribeForm',
            'Unsubscribe',
            null,
            false,
            3002
        );
        $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            'SubscribeForm',
            'Subscribe',
            null,
            false,
            3003
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            $pageId = $this->insertPage(
                ['title' => 'Newsletters', 'language' => $language]
            );

            // check if a page for mailmotor subscribe already exists in this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                [$subscribeId, $language]
            )
            ) {
                $this->insertPage(
                    ['parent_id' => $pageId, 'title' => 'Subscribe', 'language' => $language],
                    null,
                    ['extra_id' => $subscribeId, 'position' => 'main']
                );
            }

            // check if a page for mailmotor unsubscribe already exists in this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                [$unsubscribeId, $language]
            )
            ) {
                $this->insertPage(
                    ['parent_id' => $pageId, 'title' => 'Unsubscribe', 'language' => $language],
                    null,
                    ['extra_id' => $unsubscribeId, 'position' => 'main']
                );
            }
        }
    }

    private function installSettings(): void
    {
        $this->setSetting($this->getModule(), 'mail_engine', null);
        $this->setSetting($this->getModule(), 'api_key', null);
        $this->setSetting($this->getModule(), 'list_id', null);
        $this->setSetting($this->getModule(), 'double_opt_in', true);
        $this->setSetting($this->getModule(), 'overwrite_interests', false);
        $this->setSetting($this->getModule(), 'automatically_subscribe_from_form_builder_submitted_form', false);
    }
}
