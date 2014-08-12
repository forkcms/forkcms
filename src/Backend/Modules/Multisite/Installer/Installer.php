<?php

namespace Backend\Modules\Multisite\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the multisite module
 * @note: we don't add locale here, since the locale module isn't installed yet.
 *
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // add the module
        $this->addModule('Multisite');

        // import translations and database structure
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add the module in the backend navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'Multisite',
            'multisite/index',
            array(
                'multisite/add',
                'multisite/edit'
            )
        );

        $siteId = $this->insertMainSite();
        $this->insertLanguagesInSite(
            $this->getLanguages(),
            $siteId
        );

        // we do this last, because we need the sites first
        $this->setRights();

        $this->setSiteTitles();
    }

    protected function setSiteTitles()
    {
        // default titles
        $siteTitles = array(
            'en' => 'My website',
            'bg' => 'уебсайта си',
            'zh' => '我的网站',
            'cs' => 'můj web',
            'nl' => 'Mijn website',
            'fr' => 'Mon site web',
            'de' => 'Meine Webseite',
            'el' => 'ιστοσελίδα μου',
            'hu' => 'Hhonlapom',
            'it' => 'Il mio sito web',
            'ja' => '私のウェブサイト',
            'lt' => 'mano svetainė',
            'pl' => 'moja strona',
            'ro' => 'site-ul meu',
            'ru' => 'мой сайт',
            'es' => 'Mi sitio web',
            'sv' => 'min hemsida',
            'tr' => 'web siteme',
            'uk' => 'мій сайт'
        );

        // language specific
        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages() as $language) {
                // set title
                $this->setSetting(
                    'Core',
                    'site_title',
                    (isset($siteTitles[$language])) ? $siteTitles[$language] : $this->getVariable('site_title'),
                    $language,
                    $site['id']
                );
            }
        }
    }

    protected function setRights()
    {
        // add rights
        $this->setModuleRights(1, 'Multisite');
        $this->setActionRights(1, 'Multisite', 'Index');
        $this->setActionRights(1, 'Multisite', 'Add');
        $this->setActionRights(1, 'Multisite', 'Edit');
        $this->setActionRights(1, 'Multisite', 'Delete');

        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages($site['id']) as $language) {
                $this->setLanguageRights(1, $language, $site['id']);
            }
        }
    }

    /**
     * Inserts the mainsite. We use the current domain for it.
     *
     * @return int The id of the inserted site
     */
    protected function insertMainSite()
    {
        // if we're somehow installing Fork without a server,
        // fallback to a development domain
        $domain = (isset($_SERVER['HTTP_HOST'])) ?
            $_SERVER['HTTP_HOST'] :
            'fork.local'
        ;

        return $this->getDB()->insert(
            'sites',
            array(
                'domain'       => $domain,
                'is_active'    => 'Y',
                'is_viewable'  => 'Y',
                'is_main_site' => 'Y',
                'prefix'       => 'main',
            )
        );
    }

    /**
     * Inserts all selected languages in the main site
     *
     * @param array $languages Which languages do you want to insert?
     * @param int $siteId The id of the site we want to insert languages in
     */
    protected function insertLanguagesInSite(array $languages, $siteId)
    {
        foreach ($languages as $language) {
            $this->getDB()->insert(
                'sites_languages',
                array(
                    'site_id' => $siteId,
                    'language' => $language,
                    'is_active' => 'Y',
                    'is_viewable' => 'Y',
                )
            );
        }
    }
}
