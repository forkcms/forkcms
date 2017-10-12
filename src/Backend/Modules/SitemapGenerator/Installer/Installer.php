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
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        Model::get('fork.entity.create_schema')->forEntityClass(SitemapEntry::class);
    }
}
