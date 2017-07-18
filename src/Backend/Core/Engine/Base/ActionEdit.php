<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Meta;

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the edit action
 */
class ActionEdit extends Action
{
    /**
     * DataGrid with the revisions
     *
     * @var DataGridDatabase
     */
    protected $dgRevisions;

    /**
     * The form instance
     *
     * @var Form
     */
    protected $form;

    /**
     * The id of the item to edit
     *
     * @var int
     */
    protected $id;

    /**
     * The backend meta-object
     *
     * @var Meta
     */
    protected $meta;

    /**
     * The data of the item to edit
     *
     * @var array
     */
    protected $record;

    protected function parse(): void
    {
        parent::parse();

        if ($this->form) {
            $this->form->parse($this->template);
        }
    }
}
