<?php

namespace Backend\Modules\Profiles\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the profiles module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Davy Van Vooren <davy.vanvooren@netlash.com>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module.
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'profiles' as a module
        $this->addModule('Profiles');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // general settings
        $this->setSetting('Profiles', 'allow_gravatar', true);

        // add folders
        \SpoonDirectory::create(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/source/');
        \SpoonDirectory::create(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/240x240/');
        \SpoonDirectory::create(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/64x64/');
        \SpoonDirectory::create(PATH_WWW . '/src/Frontend/Files/Profiles/avatars/32x32/');

        // module rights
        $this->setModuleRights(1, 'Profiles');

        // action rights
        $this->setActionRights(1, 'Profiles', 'Add');
        $this->setActionRights(1, 'Profiles', 'AddGroup');
        $this->setActionRights(1, 'Profiles', 'AddProfileGroup');
        $this->setActionRights(1, 'Profiles', 'Block');
        $this->setActionRights(1, 'Profiles', 'DeleteGroup');
        $this->setActionRights(1, 'Profiles', 'DeleteProfileGroup');
        $this->setActionRights(1, 'Profiles', 'Delete');
        $this->setActionRights(1, 'Profiles', 'EditGroup');
        $this->setActionRights(1, 'Profiles', 'EditProfileGroup');
        $this->setActionRights(1, 'Profiles', 'Edit');
        $this->setActionRights(1, 'Profiles', 'ExportTemplate');
        $this->setActionRights(1, 'Profiles', 'Groups');
        $this->setActionRights(1, 'Profiles', 'Import');
        $this->setActionRights(1, 'Profiles', 'Index');
        $this->setActionRights(1, 'Profiles', 'MassAction');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationProfilesId = $this->setNavigation($navigationModulesId, 'Profiles');
        $this->setNavigation(
            $navigationProfilesId,
            'Overview',
            'profiles/index',
            array(
                 'profiles/add',
                 'profiles/edit',
                 'profiles/add_profile_group',
                 'profiles/edit_profile_group',
                 'profiles/import'
            )
        );
        $this->setNavigation(
            $navigationProfilesId,
            'Groups',
            'profiles/groups',
            array(
                 'profiles/add_group',
                 'profiles/edit_group'
            )
        );

        // add extra
        $activateId = $this->insertExtra('Profiles', 'block', 'Activate', 'Activate', null, 'N', 5000);
        $forgotPasswordId = $this->insertExtra(
            'Profiles',
            'block',
            'ForgotPassword',
            'ForgotPassword',
            null,
            'N',
            5001
        );
        $indexId = $this->insertExtra('Profiles', 'block', 'Dashboard', null, null, 'N', 5002);
        $loginId = $this->insertExtra('Profiles', 'block', 'Login', 'Login', null, 'N', 5003);
        $logoutId = $this->insertExtra('Profiles', 'block', 'Logout', 'Logout', null, 'N', 5004);
        $changeEmailId = $this->insertExtra('Profiles', 'block', 'ChangeEmail', 'ChangeEmail', null, 'N', 5005);
        $changePasswordId = $this->insertExtra(
            'Profiles',
            'block',
            'ChangePassword',
            'ChangePassword',
            null,
            'N',
            5006
        );
        $settingsId = $this->insertExtra('Profiles', 'block', 'Settings', 'Settings', null, 'N', 5007);
        $registerId = $this->insertExtra('Profiles', 'block', 'Register', 'Register', null, 'N', 5008);
        $resetPasswordId = $this->insertExtra('Profiles', 'block', 'ResetPassword', 'ResetPassword', null, 'N', 5008);
        $resendActivationId = $this->insertExtra(
            'Profiles',
            'block',
            'ResendActivation',
            'ResendActivation',
            null,
            'N',
            5009
        );

        $this->insertExtra('Profiles', 'widget', 'LoginBox', 'LoginBox', null, 'N', 5010);
        $this->insertExtra('Profiles', 'widget', 'LoginLink', 'LoginLink', null, 'N', 5011);

        // get search widget id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND action = ?',
            array('search', 'form')
        );

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
                array('Profiles', $language)
            )
            ) {
                // activate page
                $this->insertPage(
                    array(
                         'title' => 'Activate',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $activateId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // forgot password page
                $this->insertPage(
                    array(
                         'title' => 'Forgot password',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $forgotPasswordId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // reset password page
                $this->insertPage(
                    array(
                         'title' => 'Reset password',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $resetPasswordId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // resend activation email page
                $this->insertPage(
                    array(
                         'title' => 'Resend activation e-mail',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $resendActivationId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // login page
                $this->insertPage(
                    array(
                         'title' => 'Login',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $loginId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // register page
                $this->insertPage(
                    array(
                         'title' => 'Register',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $registerId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // logout page
                $this->insertPage(
                    array(
                         'title' => 'Logout',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $logoutId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // index page
                $indexPageId = $this->insertPage(
                    array(
                         'title' => 'Profile',
                         'type' => 'root',
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $indexId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // settings page
                $this->insertPage(
                    array(
                         'title' => 'Profile settings',
                         'parent_id' => $indexPageId,
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $settingsId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // change email page
                $this->insertPage(
                    array(
                         'title' => 'Change email',
                         'parent_id' => $indexPageId,
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $changeEmailId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );

                // change password page
                $this->insertPage(
                    array(
                         'title' => 'Change password',
                         'parent_id' => $indexPageId,
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $changePasswordId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );
            }
        }
    }
}
