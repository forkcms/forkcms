<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * This class defines the frontend, it is the core. Everything starts here.
 * We create all needed instances.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Frontend extends \KernelLoader implements \ApplicationInterface
{
    /**
     * @var Page
     */
    private $page;

    /**
     * @return Response
     */
    public function display()
    {
        return $this->page->display();
    }

    /**
     * Initializes the entire frontend; preload FB, URL, template and the requested page.
     *
     * This method exists because the service container needs to be set before
     * the page's functionality gets loaded.
     */
    public function initialize()
    {
        new Url($this->getKernel());

        // Load the rest of the page.
        $this->page = new Page($this->getKernel());
        $this->page->load();
    }
}
