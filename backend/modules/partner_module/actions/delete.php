<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a partner
 *
 * @author Jelmer Prins <jelmer@ubuntu.com>
 */
class BackendPartnerModuleDelete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');
        // does the item exist
        if ($this->id == null || !BackendPartnerModuleModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
        // get data
        $this->record = (array) BackendPartnerModuleModel::get($this->id);

        // delete item
        BackendPartnerModuleModel::delete($this->id);
        //delete the image
        SpoonFile::delete(
            FRONTEND_FILES_PATH . '/' . FrontendPartnerModuleModel::IMAGE_PATH . '/' . $this->record['img']
        );
        SpoonFile::delete(
            FRONTEND_FILES_PATH . '/' . FrontendPartnerModuleModel::THUMBNAIL_PATH . '/' . $this->record['img']
        );
        // item was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['name'])
        );
    }
}
