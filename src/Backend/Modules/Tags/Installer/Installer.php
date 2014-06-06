<?php

namespace Backend\Modules\Tags\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the tags module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // add the Tags module
        $this->addModule('Tags');

        // load database scheme and locale
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // rights
        $this->setModuleRights(1, 'Tags');
        $this->setActionRights(1, 'Tags', 'Autocomplete');
        $this->setActionRights(1, 'Tags', 'Edit');
        $this->setActionRights(1, 'Tags', 'Index');
        $this->setActionRights(1, 'Tags', 'MassAction');

        // backend navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'Tags', 'tags/index', array('tags/edit'));

        $extras = $this->insertExtras();
        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages($site['id']) as $language) {
                // check if a page for tags already exists in this language
                if (!(bool) $this->getDB()->getVar(
                    'SELECT 1
                     FROM pages AS p
                     INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                     WHERE b.extra_id = ? AND p.language = ?
                     LIMIT 1',
                    array($extras['tags'], $language)
                )
                ) {
                    $this->insertTagsPages($language, $site['id'], $textras);
                }
            }
        }
    }


    /**
     * Inserts extras
     *
     * @return array Key value pairs presenting widget => extras_id
     */
    protected function insertExtras()
    {
        $extras = array();

        // add extra
        $extras['tags'] = $this->insertExtra('Tags', 'block', 'Tags', null, null, 'N', 30);
        $this->insertExtra('Tags', 'widget', 'TagCloud', 'TagCloud', null, 'N', 31);
        $this->insertExtra('Tags', 'widget', 'Related', 'Related', null, 'N', 32);

        // get search extra id
        $extras['search'] = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            array('Search', 'widget', 'Form')
        );

        return $extras;
    }

    /**
     * Inserts pages for the tags module
     *
     * @param string $language
     * @param int $siteId
     * @param array $extras
     */
    protected function insertTagsPages($language, $siteId, $extras)
    {
        // insert contact page
        $this->insertPage(
            array(
                 'title'    => 'Tags',
                 'type'     => 'root',
                 'language' => $language,
                 'site_id'  => $siteId,
            ),
            null,
            array(
                'extra_id' => $extras['tags'],
                'position' => 'main',
            ),
            array(
                'extra_id' => $extras['search'],
                'position' => 'top',
            )
        );
    }
}
