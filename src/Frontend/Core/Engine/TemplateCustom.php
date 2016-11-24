<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

trigger_error(
    'You should use the twig templating service',
    E_USER_DEPRECATED
);

/**
 * Add all custom stuff here.
 *
 * @deprecated
 */
class TemplateCustom
{
    /**
     * Template instance
     *
     * @var Template
     */
    private $tpl;

    /**
     * @param Template $tpl The template instance.
     */
    public function __construct($tpl)
    {
        $this->tpl = $tpl;
        $this->parse();
    }

    /**
     * Parse the custom stuff
     */
    private function parse()
    {
        // insert your custom stuff here...
    }
}
