<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * This class defines the backend, it is the core. Everything starts here.
 * We create all needed instances and execute the requested action
 */
class Backend extends \KernelLoader implements \ApplicationInterface
{
    /**
     * @var Base\Action
     */
    private $action;

    /**
     * @return Response
     */
    public function display()
    {
        return $this->action->execute();
    }

    /**
     * This method exists because the service container needs to be set before
     * the page's functionality gets loaded.
     */
    public function initialize()
    {
        $url = new Url($this->getKernel());

        new TwigTemplate();
        new Navigation($this->getKernel());
        new Header($this->getKernel());

        $this->action = new Action($this->getKernel());
        $this->action->setModule($url->getModule());
        $this->action->setAction($url->getAction());
    }
}
