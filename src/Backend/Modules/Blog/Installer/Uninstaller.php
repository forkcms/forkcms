<?php

namespace Backend\Modules\Blog\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\AbstractModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the blog module
 */
class Uninstaller extends AbstractModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Blog');

        $this->deleteBackendWidgets();
        $this->deleteBackendNavigation();

        $this->dropDatabaseTables(['blog_comments', 'blog_posts', 'blog_categories']);
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('/Settings/Modules/' . $this->getModule());
        $this->deleteNavigation('/Modules/' . $this->getModule() . '/Articles');
        $this->deleteNavigation('/Modules/' . $this->getModule() . '/Comments');
        $this->deleteNavigation('/Modules/' . $this->getModule() . '/Categories');
        $this->deleteNavigation('/Modules/' . $this->getModule());
    }

    private function deleteBackendWidgets(): void
    {
        $this->deleteDashboardWidgets('Blog', ['Comments']);
    }
}
