<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

use Backend\Core\Engine\Form;
use Backend\Core\Engine\Meta;

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the add action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class ActionAdd extends Action
{
    /**
     * The form instance
     *
     * @var Form
     */
    protected $frm;

    /**
     * The backends meta-object
     *
     * @var Meta
     */
    protected $meta;

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        if ($this->frm) {
            $this->frm->parse($this->tpl);
        }
    }
}
