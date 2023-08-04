<?php

namespace ForkCMS\Modules\Backend\Installer;

use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItem;
use ForkCMS\Modules\Backend\Domain\User\Command\CreateUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
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
