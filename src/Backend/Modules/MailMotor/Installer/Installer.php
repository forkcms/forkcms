<?php

namespace Backend\Modules\MailMotor\Installer;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller as ModuleInstaller;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Modules\MailMotor\Engine\ApiCall as ApiCall;

/**
 * Installer for the MailMotor moduleJobCurrentState
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // install settings
        $this->installSettings();

        // install locale
        $this->importLocale(dirname(__FILE__) . '/data/locale.xml');

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
        $this->setModuleRights(1, 'MailMotor');

        // action rights
        $this->setActionRights(1, 'MailMotor', 'Settings');
    }

    /**
     * Install backend navigation
     */
    private function installBackendNavigation()
    {
        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'mail_motor/settings');
    }

    /**
     * Install the pages for this module
     */
    private function installPages()
    {
        // add extra's
        $subscribeId = $this->insertExtra('MailMotor', 'block', 'SubscribeForm', 'Subscribe', null, 'N', 3001);
        $unsubscribeId = $this->insertExtra('MailMotor', 'block', 'UnsubscribeForm', 'Unsubscribe', null, 'N', 3002);
        $this->insertExtra('MailMotor', 'widget', 'SubscribeForm', 'Subscribe', null, 'N', 3003);

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
        // add 'MailMotor' as a module
        $this->addModule('MailMotor', 'The module that allows you to insert/delete subscribers to/from your mailinglist.');

        // module settings
        $this->setSetting('MailMotor', 'mail_engine', null);
        $this->setSetting('MailMotor', 'api_key', null);
        $this->setSetting('MailMotor', 'list_id', null);
    }
}
