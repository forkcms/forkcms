<?php

namespace Backend\Modules\Profiles\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Language\Language;

/**
 * Installer for the profiles module.
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'profiles' as a module
        $this->addModule('Profiles');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // general settings
        $this->setSetting($this->getModule(), 'allow_gravatar', true);
        $this->setSetting($this->getModule(), 'overwrite_profile_notification_email', false);
        $this->setSetting($this->getModule(), 'profile_notification_email', null);
        $this->setSetting($this->getModule(), 'send_mail_for_new_profile_to_admin', false);
        $this->setSetting($this->getModule(), 'send_mail_for_new_profile_to_profile', false);

        // add folders
        $filesystem = new Filesystem();
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/source/');
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/240x240/');
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/64x64/');
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/32x32/');

        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'AddGroup');
        $this->setActionRights(1, $this->getModule(), 'AddProfileGroup');
        $this->setActionRights(1, $this->getModule(), 'Block');
        $this->setActionRights(1, $this->getModule(), 'DeleteGroup');
        $this->setActionRights(1, $this->getModule(), 'DeleteProfileGroup');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'EditGroup');
        $this->setActionRights(1, $this->getModule(), 'EditProfileGroup');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'ExportTemplate');
        $this->setActionRights(1, $this->getModule(), 'Groups');
        $this->setActionRights(1, $this->getModule(), 'Import');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'MassAction');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationProfilesId = $this->setNavigation($navigationModulesId, 'Profiles');
        $this->setNavigation(
            $navigationProfilesId,
            'Overview',
            'profiles/index',
            [
                'profiles/add',
                'profiles/edit',
                'profiles/add_profile_group',
                'profiles/edit_profile_group',
                'profiles/import',
            ]
        );
        $this->setNavigation(
            $navigationProfilesId,
            'Groups',
            'profiles/groups',
            [
                'profiles/add_group',
                'profiles/edit_group',
            ]
        );

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Profiles', 'profiles/settings');

        // add extra
        $activateId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Activate',
            'Activate',
            null,
            false,
            5000
        );
        $forgotPasswordId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ForgotPassword',
            'ForgotPassword',
            null,
            false,
            5001
        );
        $indexId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Dashboard',
            null,
            null,
            false,
            5002
        );
        $loginId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Login',
            'Login',
            null,
            false,
            5003
        );
        $logoutId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Logout',
            'Logout',
            null,
            false,
            5004
        );
        $changeEmailId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ChangeEmail',
            'ChangeEmail',
            null,
            false,
            5005
        );
        $changePasswordId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ChangePassword',
            'ChangePassword',
            null,
            false,
            5006
        );
        $settingsId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Settings',
            'Settings',
            null,
            false,
            5007
        );
        $registerId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Register',
            'Register',
            null,
            false,
            5008
        );
        $resetPasswordId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ResetPassword',
            'ResetPassword',
            null,
            false,
            5008
        );
        $resendActivationId = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ResendActivation',
            'ResendActivation',
            null,
            false,
            5009
        );

        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'LoginBox', 'LoginBox', null, false, 5010);
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'LoginLink', 'LoginLink', null, false, 5011);
        $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            'SecurePage',
            'SecurePage',
            null,
            false,
            5012
        );

        // get search widget id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND action = ?',
            ['search', 'form']
        );

        $originalLocale = Language::getInterfaceLanguage();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // only add pages if profiles isn't linked anywhere
            // @todo refactor me, syntax sucks atm
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 INNER JOIN modules_extras AS e ON e.id = b.extra_id
                 WHERE e.module = ? AND p.language = ?
                 LIMIT 1',
                [$this->getModule(), $language]
            )
            ) {
                // We must define the locale we want to insert the page into
                Language::setLocale($language);

                // activate page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('Activate')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $activateId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // forgot password page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('ForgotPassword')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $forgotPasswordId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // reset password page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('ResetPassword')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $resetPasswordId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // resend activation email page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('ResendActivation')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $resendActivationId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // login page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('Login')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $loginId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // register page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('Register')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $registerId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // logout page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('Logout')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $logoutId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // index page
                $indexPageId = $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('Profile')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $indexId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // settings page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('ProfileSettings')),
                        'parent_id' => $indexPageId,
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $settingsId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // change email page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('ChangeEmail')),
                        'parent_id' => $indexPageId,
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $changeEmailId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );

                // change password page
                $this->insertPage(
                    [
                        'title' => ucfirst(Language::lbl('ChangePassword')),
                        'parent_id' => $indexPageId,
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $changePasswordId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );
            }
        }

        // restore the original locale
        if (!empty($originalLocale)) {
            Language::setLocale($originalLocale);
        }
    }
}
