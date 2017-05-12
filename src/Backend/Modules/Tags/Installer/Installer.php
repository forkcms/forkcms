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
    public function install(): void
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'blog' as a module
        $this->addModule('Tags');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'Autocomplete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'MassAction');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'Tags', 'tags/index', ['tags/edit']);

        // add extra
        $tagsID = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'Tags', null, null, false, 30);
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'TagCloud', 'TagCloud', null, false, 31);
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Related', 'Related', null, false, 32);

        // get search extra id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            ['Search', ModuleExtraType::widget(), 'Form']
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
                [$tagsID, $language]
            )
            ) {
                // insert contact page
                $this->insertPage(
                    [
                        'title' => 'Tags',
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $tagsID, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );
            }
        }
    }
}
