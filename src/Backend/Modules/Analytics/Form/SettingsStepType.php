<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\TwigTemplate;

/**
 * An interface so we can split the settings form up into multiple forms
 */
interface SettingsStepType
{
    /**
     * @param TwigTemplate $template
     */
    public function parse(TwigTemplate $template);

    /**
     * @return bool
     */
    public function handle();
}
