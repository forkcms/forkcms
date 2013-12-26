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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the edit action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class ActionEdit extends Action
{
    /**
     * DataGrid with the revisions
     *
     * @var BackendDataGridDB
     */
    protected $dgRevisions;

    /**
     * The form instance
     *
     * @var BackendForm
     */
    protected $frm;

    /**
     * The id of the item to edit
     *
     * @var int
     */
    protected $id;

    /**
     * The backends meta-object
     *
     * @var BackendMeta
     */
    protected $meta;

    /**
     * The data of the item to edit
     *
     * @var array
     */
    protected $record;

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        if($this->frm) $this->frm->parse($this->tpl);
    }
}
