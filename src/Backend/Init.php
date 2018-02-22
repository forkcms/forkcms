<?php

namespace App\Backend;

use App\Common\Core\Init as BaseInit;
use SpoonFilter;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class will initiate the backend-application
 */
class Init extends BaseInit
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
