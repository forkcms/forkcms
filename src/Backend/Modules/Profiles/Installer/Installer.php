<?php

namespace Backend\Modules\Profiles\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Group\Group;
use Backend\Modules\Profiles\Domain\GroupRight\GroupRight;
use Backend\Modules\Profiles\Domain\Session\Session;
use Backend\Modules\Profiles\Domain\Setting\Setting;
use ForkCMS\Bundle\InstallerBundle\Language\Locale;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use SpoonFilter;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Language\Language;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\PageRepository;

/**
 * Installer for the profiles module.
 */
class Installer extends ModuleInstaller
{
    /** @var array */
    private $extraIds;

    public function install(): void
    {
        $this->addModule('Profiles');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureEntities();
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendFilesDirectories();
        $this->configureFrontendPages();
    }

    private function configureBackendActionRightsForGroup(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddGroup');
        $this->setActionRights(1, $this->getModule(), 'DeleteGroup');
        $this->setActionRights(1, $this->getModule(), 'EditGroup');
        $this->setActionRights(1, $this->getModule(), 'Groups');
    }

    private function configureBackendActionRightsForProfile(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Block');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'ExportTemplate');
        $this->setActionRights(1, $this->getModule(), 'Import');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'MassAction');
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function configureBackendActionRightsForProfileGroup(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddProfileGroup');
        $this->setActionRights(1, $this->getModule(), 'DeleteProfileGroup');
        $this->setActionRights(1, $this->getModule(), 'EditProfileGroup');
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
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

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Profiles', 'profiles/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->configureBackendActionRightsForGroup();
        $this->configureBackendActionRightsForProfile();
        $this->configureBackendActionRightsForProfileGroup();
    }

    private function configureFrontendExtras(): void
    {
        $this->extraIds['activate'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Activate',
            'Activate'
        );
        $this->extraIds['forgot_password'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ForgotPassword',
            'ForgotPassword'
        );
        $this->extraIds['index'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Dashboard'
        );
        $this->extraIds['login'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Login',
            'Login'
        );
        $this->extraIds['logout'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Logout',
            'Logout'
        );
        $this->extraIds['change_email'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ChangeEmail',
            'ChangeEmail'
        );
        $this->extraIds['change_password'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ChangePassword',
            'ChangePassword'
        );
        $this->extraIds['settings'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Settings',
            'Settings'
        );
        $this->extraIds['register'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Register',
            'Register'
        );
        $this->extraIds['reset_password'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ResetPassword',
            'ResetPassword'
        );
        $this->extraIds['resend_activation'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'ResendActivation',
            'ResendActivation'
        );

        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'LoginBox', 'LoginBox');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'LoginLink', 'LoginLink');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'SecurePage', 'SecurePage');
    }

    private function configureFrontendFilesDirectories(): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/source/');
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/240x240/');
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/64x64/');
        $filesystem->mkdir(PATH_WWW . '/src/Frontend/Files/Profiles/Avatars/32x32/');
    }

    private function configureFrontendPages(): void
    {
        $originalLocale = Language::getInterfaceLanguage();

        // get search widget id
        $searchExtraId = $this->getSearchWidgetId();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // We must define the locale we want to insert the page into
            Language::setLocale($language);
            $locale = Locale::fromString($language);

            // index page
            if (!$this->hasPageWithProfilesBlock($locale)) {
                $indexPageId = $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('Profile')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('index'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );

                // settings page
                if (!$this->hasPageWithProfilesAction($locale, 'Settings')) {
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst(Language::lbl('ProfileSettings')),
                            'parent_id' => $indexPageId,
                            'language' => $language,
                        ],
                        null,
                        ['extra_id' => $this->getExtraId('settings'), 'position' => 'main'],
                        ['extra_id' => $searchExtraId, 'position' => 'top']
                    );
                }

                // change email page
                if (!$this->hasPageWithProfilesAction($locale, 'ChangeEmail')) {
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst(Language::lbl('ChangeEmail')),
                            'parent_id' => $indexPageId,
                            'language' => $language,
                        ],
                        null,
                        ['extra_id' => $this->getExtraId('change_email'), 'position' => 'main'],
                        ['extra_id' => $searchExtraId, 'position' => 'top']
                    );
                }

                // change password page
                if (!$this->hasPageWithProfilesAction($locale, 'ChangePassword')) {
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst(Language::lbl('ChangePassword')),
                            'parent_id' => $indexPageId,
                            'language' => $language,
                        ],
                        null,
                        ['extra_id' => $this->getExtraId('change_password'), 'position' => 'main'],
                        ['extra_id' => $searchExtraId, 'position' => 'top']
                    );
                }
            }

            // activate page
            if (!$this->hasPageWithProfilesAction($locale, 'Activate')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('Activate')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('activate'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }

            // forgot password page
            if (!$this->hasPageWithProfilesAction($locale, 'ForgotPassword')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('ForgotPassword')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('forgot_password'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }

            // reset password page
            if (!$this->hasPageWithProfilesAction($locale, 'ResetPassword')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('ResetPassword')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('reset_password'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }

            // resend activation email page
            if (!$this->hasPageWithProfilesAction($locale, 'ResendActivation')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('ResendActivation')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('resend_activation'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }

            // login page
            if (!$this->hasPageWithProfilesAction($locale, 'Login')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('Login')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('login'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }

            // register page
            if (!$this->hasPageWithProfilesAction($locale, 'Register')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('Register')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('register'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }

            // logout page
            if (!$this->hasPageWithProfilesAction($locale, 'Logout')) {
                $this->insertPage(
                    [
                        'title' => SpoonFilter::ucfirst(Language::lbl('Logout')),
                        'type' => 'root',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->getExtraId('logout'), 'position' => 'main'],
                    ['extra_id' => $searchExtraId, 'position' => 'top']
                );
            }
        }

        // restore the original locale
        if (!empty($originalLocale)) {
            Language::setLocale($originalLocale);
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'allow_gravatar', true);
        $this->setSetting($this->getModule(), 'overwrite_profile_notification_email', false);
        $this->setSetting($this->getModule(), 'profile_notification_email', null);
        $this->setSetting($this->getModule(), 'send_mail_for_new_profile_to_admin', false);
        $this->setSetting($this->getModule(), 'send_mail_for_new_profile_to_profile', false);
        $this->setSetting($this->getModule(), 'limit_display_name_changes', true);
        $this->setSetting($this->getModule(), 'max_display_name_changes', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES);
    }

    private function getExtraId(string $key): int
    {
        if (!array_key_exists($key, $this->extraIds)) {
            throw new \Exception('Key not set yet, please check your installer.');
        }

        return $this->extraIds[$key];
    }

    private function getSearchWidgetId(): int
    {
        /** @var ModuleExtraRepository $moduleExtraRepository */
        $moduleExtraRepository = Model::get(ModuleExtraRepository::class);
        $widgetId = $moduleExtraRepository->getModuleExtraId('Search', 'Form', ModuleExtraType::widget());

        if ($widgetId === null) {
            throw new \RuntimeException('Could not find Search Widget');
        }

        return $widgetId;
    }

    private function hasPageWithProfilesBlock(Locale $locale): bool
    {
        $pageRepository = Model::getContainer()->get(PageRepository::class);

        return $pageRepository->pageExistsWithModuleBlockForLocale('Profiles', $locale);
    }

    private function hasPageWithProfilesAction(Locale $locale, string $action): bool
    {
        $pageRepository = Model::getContainer()->get(PageRepository::class);

        return $pageRepository->pageExistsWithModuleActionForLocale('Profiles', $action, $locale);
    }

    private function configureEntities(): void
    {
        Model::get('fork.entity.create_schema')->forEntityClasses(
            [
                Profile::class,
                Group::class,
                GroupRight::class,
                Session::class,
                Setting::class,
            ]
        );
    }
}
