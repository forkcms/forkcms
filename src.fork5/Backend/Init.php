<?php

namespace Backend;

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
}
