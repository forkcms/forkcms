<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will install the partners module.
 *
 * @author Jelmer Prins <jelmer@cumocoders.be>
 */
class PartnersInstaller extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        //$this->importSQL(dirname(__FILE__) . '/data/install.sql');@todo uncomment this when finished

        $this->addModule('partners');

        $this->importLocale(dirname(__FILE__) . '/data/locale.xml');

        $this->makeSearchable('partners');
        $this->setModuleRights(1, 'partners');

        $this->setActionRights(1, 'partners', 'index');
        $this->setActionRights(1, 'partners', 'addWidget');
        $this->setActionRights(1, 'partners', 'editWidget');
        $this->setActionRights(1, 'partners', 'deleteWidget');
        $this->setActionRights(1, 'partners', 'widget');
        $this->setActionRights(1, 'partners', 'add');
        $this->setActionRights(1, 'partners', 'edit');
        $this->setActionRights(1, 'partners', 'delete');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'Partners',
            'partners/index'
        );

        SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . FrontendPartnersModel::THUMBNAIL_PATH);
    }
}
