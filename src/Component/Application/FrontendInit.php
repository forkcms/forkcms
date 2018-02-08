<?php

namespace App\Component\Application;

/**
 * This class will initiate the frontend-application
 */
class FrontendInit extends Init
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
