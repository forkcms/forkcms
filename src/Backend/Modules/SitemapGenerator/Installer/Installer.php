<?php

namespace Backend\Modules\SitemapGenerator\Installer;

use Backend\Core\Installer\ModuleInstaller;

class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('SitemapGenerator');
    }
}
