<?php

namespace Backend\Modules\ContentBlocks\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;

/**
 * Installer for the content blocks module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // add 'ContentBlocks' as a module
        $this->addModule('ContentBlocks');

        // import locale and add DB column.
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');
        $this->addEntityInDatabase(BackendContentBlocksModel::ENTITY_CLASS);

        // general settings
        $this->setSetting($this->getModule(), 'max_num_revisions', 20);

        // module and action rights
        $this->setModuleRights(1, $this->getModule());
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'content_blocks/index', array('content_blocks/add', 'content_blocks/edit'));
    }
}
