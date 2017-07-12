<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Form;
use Backend\Core\Engine\Meta;

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the add action
 */
class ActionAdd extends Action
{
    /**
     * The form instance
     *
     * @var Form
     */
    protected $form;

    /**
     * The backend meta-object
     *
     * @var Meta
     */
    protected $meta;

    protected function parse(): void
    {
        parent::parse();

        if ($this->form) {
            $this->form->parse($this->template);
        }
    }
}
