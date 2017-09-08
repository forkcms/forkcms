<?php

namespace Backend\Modules\Profiles\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\AbstractModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;
use Backend\Core\Language\Language;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Uninstaller for the profiles module.
 */
class Uninstaller extends AbstractModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Profiles');

        $this->deleteFrontendFilesDirectories();
        $this->deleteFrontendPages();
        $this->deleteBackendNavigation();

        $this->dropDatabaseTables([
            'profiles_settings',
            'profiles_sessions',
            'profiles_groups_rights',
            'profiles_groups',
            'profiles',
        ]);

        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule() . '.Overview');
        $this->deleteNavigation('Modules.' . $this->getModule() . '.Groups');
        $this->deleteNavigation('Modules.' . $this->getModule());
        $this->deleteNavigation('Settings.Modules.' . $this->getModule());
    }

    private function deleteFrontendFilesDirectories(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/source/');
        $filesystem->remove(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/240x240/');
        $filesystem->remove(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/64x64/');
        $filesystem->remove(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/32x32/');
    }

    private function deleteFrontendPages(): void
    {
        foreach ($this->getLanguages() as $language) {
            // We must define the locale we want to insert the page into
            Language::setLocale($language);

            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->deletePages([
                ucfirst(Language::lbl('Profile')),
                ucfirst(Language::lbl('Activate')),
                ucfirst(Language::lbl('ForgotPassword')),
                ucfirst(Language::lbl('ResetPassword')),
                ucfirst(Language::lbl('ResendActivation')),
                ucfirst(Language::lbl('Login')),
                ucfirst(Language::lbl('Register')),
                ucfirst(Language::lbl('Logout')),
                ucfirst(Language::lbl('ProfileSettings')),
                ucfirst(Language::lbl('ChangeEmail')),
                ucfirst(Language::lbl('ChangePassword')),
            ]);
        }
    }
}
