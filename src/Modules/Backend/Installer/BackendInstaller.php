<?php

namespace ForkCMS\Modules\Backend\Installer;

use ForkCMS\Modules\Backend\Backend\Actions\Dashboard;
use ForkCMS\Modules\Backend\Backend\Actions\UserAdd;
use ForkCMS\Modules\Backend\Backend\Actions\UserDelete;
use ForkCMS\Modules\Backend\Backend\Actions\UserEdit;
use ForkCMS\Modules\Backend\Backend\Actions\UserGroupAdd;
use ForkCMS\Modules\Backend\Backend\Actions\UserGroupDelete;
use ForkCMS\Modules\Backend\Backend\Actions\UserGroupEdit;
use ForkCMS\Modules\Backend\Backend\Actions\UserGroupIndex;
use ForkCMS\Modules\Backend\Backend\Actions\UserIndex;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItem;
use ForkCMS\Modules\Backend\Domain\User\Command\CreateUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Backend\Backend\Actions\ModuleSettings;
use Symfony\Bridge\Doctrine\Security\RememberMe\DoctrineTokenProvider;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

final class BackendInstaller extends ModuleInstaller
{
    public const IS_REQUIRED = true;

    public function preInstall(): void
    {
        $this->createTableForEntities(
            User::class,
            UserGroup::class,
            NavigationItem::class,
        );
        $this->createRememberMeTable();
        $this->createAdminUser();
    }

    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->createBackendPages();

        // Generate a random 256 bits key as string
        $key = bin2hex(random_bytes(32));
        $this->setSetting('2fa_key', $key);
    }

    private function createBackendPages(): void
    {
        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Dashboard'),
            slug: Dashboard::getActionSlug(),
            sequence: 0,
        );
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Users'),
            UserIndex::getActionSlug(),
            $this->getSettingsNavigationItem(),
            [
                UserAdd::getActionSlug(),
                UserEdit::getActionSlug(),
                UserDelete::getActionSlug(),
            ],
        );
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Groups'),
            UserGroupIndex::getActionSlug(),
            $this->getSettingsNavigationItem(),
            [
                UserGroupAdd::getActionSlug(),
                UserGroupEdit::getActionSlug(),
                UserGroupDelete::getActionSlug(),
            ],
        );

        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('2FA'),
            ModuleSettings::getActionSlug(),
            $this->getSettingsNavigationItem()
        );
    }

    private function createAdminUser(): void
    {
        $installerConfiguration = InstallerConfiguration::fromCache();

        $createUser = new CreateUser();
        $createUser->email = $installerConfiguration->getAdminEmail();
        $createUser->plainTextPassword = $installerConfiguration->getAdminPassword();
        $createUser->displayName = 'Fork CMS';
        $createUser->superAdmin = true;
        $createUser->accessToBackend = true;
        $createUser->userGroups->add($this->userGroupRepository->getAdminUserGroup());
        $createUser->settings->set('locale', $installerConfiguration->getDefaultUserLocale()->value);
        $createUser->settings->set('date_format_short', $_ENV['FORK_DEFAULT_DATE_FORMAT_SHORT']);
        $createUser->settings->set('date_format_long', $_ENV['FORK_DEFAULT_DATE_FORMAT_LONG']);
        $createUser->settings->set('time_format', $_ENV['FORK_DEFAULT_TIME_FORMAT']);
        $createUser->settings->set('number_format', $_ENV['FORK_DEFAULT_NUMBER_FORMAT']);
        $createUser->settings->set('date_time_order', $_ENV['FORK_DEFAULT_DATE_TIME_ORDER']);
        $this->dispatchCommand($createUser);

        $user = $createUser->getEntity();

        // Authenticate the created user
        $this->tokenStorage->setToken(
            new PreAuthenticatedToken(
                $user,
                'backend',
                $user->getRoles()
            )
        );
    }

    private function createRememberMeTable(): void
    {
        $connection = $this->entityManager->getConnection();
        $doctrineTokenProvider = new DoctrineTokenProvider($connection);
        $schema = $connection->createSchemaManager()->introspectSchema();
        $doctrineTokenProvider->configureSchema($schema, $connection);
        $connection->createSchemaManager()->migrateSchema($schema);
    }
}
