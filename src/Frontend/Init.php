<?php

namespace ForkCMS\Frontend;

use ForkCMS\Common\Core\Init as BaseInit;

/**
 * This class will initiate the frontend-application
 */
class Init extends BaseInit
{
    protected $allowedTypes = ['Frontend', 'FrontendAjax'];

    /**
     * @param string $type The type of init to load, possible values are: frontend, frontend_ajax, frontend_js.
     */
    public function initialize(string $type): void
    {
        parent::initialize($type);

        \SpoonFilter::disableMagicQuotes();
    }
}
