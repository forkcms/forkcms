<?php

namespace Backend\Modules\Mailmotor\Installer;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller as ModuleInstaller;

/**
 * Installer for the Mailmotor module
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        $this->addModule('Mailmotor', 'The module that allows you to insert/delete subscribers to/from your mailinglist.');

        // install settings
        $this->installSettings();

        // install locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // install the mailmotor module
        $this->installModule();

        // install backend navigation
        $this->installBackendNavigation();

        // install the pages for the module
        $this->installPages();
    }

    /**
     * Install the module and it's actions
     */
    private function installModule()
    {
        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    /**
     * Install backend navigation
     */
    private function installBackendNavigation()
    {
        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'mailmotor/settings');
    }

    /**
     * Install the pages for this module
     */
    private function installPages()
    {
        // add extra's
        $subscribeId = $this->insertExtra($this->getModule(), 'block', 'SubscribeForm', 'Subscribe', null, 'N', 3001);
        $unsubscribeId = $this->insertExtra($this->getModule(), 'block', 'UnsubscribeForm', 'Unsubscribe', null, 'N', 3002);
        $this->insertExtra($this->getModule(), 'widget', 'SubscribeForm', 'Subscribe', null, 'N', 3003);

        // loop languages
        foreach ($this->getLanguages() as $language) {
            $pageId = $this->insertPage(
                array('title' => 'Newsletters', 'language' => $language)
            );

            // check if a page for mailmotor subscribe already exists in this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($subscribeId, $language)
            )) {
                $this->insertPage(
                    array('parent_id' => $pageId, 'title' => 'Subscribe', 'language' => $language),
                    null,
                    array('extra_id' => $subscribeId, 'position' => 'main')
                );
            }

            // check if a page for mailmotor unsubscribe already exists in this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($unsubscribeId, $language)
            )) {
                $this->insertPage(
                    array('parent_id' => $pageId, 'title' => 'Unsubscribe', 'language' => $language),
                    null,
                    array('extra_id' => $unsubscribeId, 'position' => 'main')
                );
            }
        }
    }

    /**
     * Install settings
     */
    private function installSettings()
    {
        $this->setSetting($this->getModule(), 'mail_engine', null);
        $this->setSetting($this->getModule(), 'api_key', null);
        $this->setSetting($this->getModule(), 'list_id', null);
        $this->setSetting($this->getModule(), 'overwrite_interests', false);
        $this->setSetting($this->getModule(), 'automatically_subscribe_from_form_builder_submitted_form', false);
    }
}
