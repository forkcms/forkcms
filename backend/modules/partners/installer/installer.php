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
        $this->importSQL(dirname(__FILE__) . '/data/install.sql');

        $this->addModule('partners');

        $this->importLocale(dirname(__FILE__) . '/data/locale.xml');

        $this->makeSearchable('partners');

        $this->setModuleRights(1, 'partners');
        $this->setActionRights(1, 'partners', 'index');
        $this->setActionRights(1, 'partners', 'add');
        $this->setActionRights(1, 'partners', 'edit');
        $this->setActionRights(1, 'partners', 'delete');
        $this->setActionRights(1, 'partners', 'addPartner');
        $this->setActionRights(1, 'partners', 'editPartner');
        $this->setActionRights(1, 'partners', 'deletePartner');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'Partners',
            'partners/index',
            array(
                'partners/add',
                'partners/add_partner',
                'partners/edit',
                'partners/edit_partner',
                'partners/index'
            )
        );

        SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . FrontendPartnersModel::IMAGE_PATH);
    }
}
