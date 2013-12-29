<?php

namespace Frontend\Core\Engine\Base;

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
 * This class will be the base of the objects used in on-site
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Object extends \KernelLoader
{
    /**
     * Template instance
     *
     * @var    FrontendTemplate
     */
    protected $tpl;

    /**
     * URL instance
     *
     * @var    FrontendURL
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

        $this->tpl = $this->getContainer()->get('template');
        $this->URL = $this->getContainer()->get('url');
    }
}
