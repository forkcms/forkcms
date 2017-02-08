<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\TwigTemplate;
use Frontend\Core\Engine\Url;

/**
 * This class will be the base of the objects used in on-site
 */
class Object extends KernelLoader
{
    /**
     * TwigTemplate instance
     *
     * @var TwigTemplate
     */
    protected $tpl;

    /**
     * URL instance
     *
     * @var Url
     */
    protected $URL;

    /**
     * It will grab stuff from the reference.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->tpl = $this->getContainer()->get('templating');
        $this->URL = $this->getContainer()->get('url');
    }
}
