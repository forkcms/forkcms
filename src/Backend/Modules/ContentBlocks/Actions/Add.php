<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;
use Backend\Modules\ContentBlocks\Form\Type\ContentBlocksType;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * The available templates
     *
     * @var	array
     */
    private $templates = array();

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = $this->createForm(new ContentBlocksType());
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isValid()) {
            $item = $this->frm->getData();
            $item['id'] = BackendContentBlocksModel::getMaximumId() + 1;

            // insert the item
            $item['revision_id'] = BackendContentBlocksModel::insert($item);

            // trigger event
            BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

            // everything is saved, so redirect to the overview
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&report=added&var=' .
                urlencode($item['title']) . '&highlight=row-' . $item['id']
            );
        }
    }
}
