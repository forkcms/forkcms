<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will install the partner_module.
 *
 * @author Jelmer Prins <jelmer@cumocoders.be>
 */
class PartnerModuleInstaller extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        $this->importSQL(dirname(__FILE__) . '/data/install.sql');

        $this->addModule('partner_module');

        $this->importLocale(dirname(__FILE__) . '/data/locale.xml');

        $this->makeSearchable('partner_module');
        $this->setModuleRights(1, 'partner_module');

        $this->setActionRights(1, 'partner_module', 'index');
        $this->setActionRights(1, 'partner_module', 'add');
        $this->setActionRights(1, 'partner_module', 'edit');
        $this->setActionRights(1, 'partner_module', 'delete');
        $this->insertExtra('partner_module', 'widget', 'Slideshow', 'slideshow');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationBlogId = $this->setNavigation(
            $navigationModulesId,
            'PartnerModule',
            'partner_module/index',
            array('partner_module/add', 'partner_module/edit')
        );

        SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . FrontendPartnerModuleModel::THUMBNAIL_PATH);
    }
}
