<?php

namespace App\Component\Application;

use SpoonFilter;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class will initiate the backend-application
 */
class BackendInit extends Init
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
