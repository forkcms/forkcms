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

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the delete action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class ActionDelete extends Action
{
    /**
     * The id of the item to edit
     *
     * @var int
     */
    protected $id;

    /**
     * The data of the item to edit
     *
     * @var array
     */
    protected $record;

    /**
     * Execute the current action
     * This method will be overwritten in most of the actions, but still be called to add general stuff
     */
    public function execute()
    {
        parent::parse();
        parent::checkToken();
    }
}
