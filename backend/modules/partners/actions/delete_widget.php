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
class BackendPartnersDeleteWidget extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');
        // does the item exist
        if ($this->id == null || !BackendPartnersModel::widgetExists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
        // get data
        $this->record = (array) BackendPartnersModel::getWidget($this->id);

        // delete item
        BackendPartnersModel::deleteWidget($this->record['id'], $this->record['widget_id']);

        // item was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['name'])
        );
    }
}
