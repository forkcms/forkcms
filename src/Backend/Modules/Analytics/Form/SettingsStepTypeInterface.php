<?php

namespace App\Backend\Modules\Analytics\Form;

use App\Backend\Core\Engine\TwigTemplate;

/**
 * An interface so we can split the settings form up into multiple forms
 */
interface SettingsStepTypeInterface
{
    public function parse(TwigTemplate $template): void;

    public function handle(): bool;
}
