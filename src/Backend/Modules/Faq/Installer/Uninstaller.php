<?php

namespace Backend\Modules\Faq\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the faq module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Faq');

        $this->deleteFrontendPages();
        $this->deleteBackendWidgets();
        $this->deleteBackendNavigation();

        $this->dropDatabase(['faq_feedback', 'faq_questions', 'faq_categories']);
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule() . '.Categories');
        $this->deleteNavigation('Modules.' . $this->getModule() . '.Questions');
        $this->deleteNavigation('Modules.' . $this->getModule());
        $this->deleteNavigation('Settings.Modules.' . $this->getModule());
    }

    private function deleteBackendWidgets(): void
    {
        $this->deleteDashboardWidgets($this->getModule(), ['Feedback']);
    }

    private function deleteFrontendPages(): void
    {
        $this->deletePages(['FAQ']);
    }
}
