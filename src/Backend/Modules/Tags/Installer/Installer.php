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
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'blog' as a module
        $this->addModule('Tags');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Tags');

        // action rights
        $this->setActionRights(1, 'Tags', 'Autocomplete');
        $this->setActionRights(1, 'Tags', 'Edit');
        $this->setActionRights(1, 'Tags', 'Index');
        $this->setActionRights(1, 'Tags', 'MassAction');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'Tags', 'tags/index', array('tags/edit'));

        // add extra
        $tagsID = $this->insertExtra('Tags', 'block', 'Tags', null, null, 'N', 30);
        $this->insertExtra('Tags', 'widget', 'TagCloud', 'TagCloud', null, 'N', 31);
        $this->insertExtra('Tags', 'widget', 'Related', 'Related', null, 'N', 32);

        // get search extra id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            array('Search', 'widget', 'Form')
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if a page for tags already exists in this language
            // @todo refactor this if statement
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($tagsID, $language)
            )
            ) {
                // insert contact page
                $this->insertPage(
                    array(
                         'title' => 'Tags',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $tagsID, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );
            }
        }
    }
}
