<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\TwigTemplate;

/**
 * An interface so we can split the settings form up into multiple forms
 */
interface SettingsStepTypeInterface
{
    public function parse(TwigTemplate $template): void;

    public function handle(): bool;
}
