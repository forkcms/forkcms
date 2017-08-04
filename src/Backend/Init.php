<?php

namespace Backend;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use SpoonFilter;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class will initiate the backend-application
 */
class Init extends \Common\Core\Init
{
    public function __construct(KernelInterface $kernel)
    {
        $this->allowedTypes = ['Backend', 'BackendAjax', 'Console'];

        parent::__construct($kernel);
    }

    public function initialize(string $type): void
    {
        parent::initialize($type);

        SpoonFilter::disableMagicQuotes();
    }
}
