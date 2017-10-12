<?php

namespace Backend\Modules\SitemapGenerator\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\SitemapGenerator\Domain\SitemapEntry;

class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('SitemapGenerator');
        Model::get('fork.entity.create_schema')->forEntityClass(SitemapEntry::class);
    }
}
