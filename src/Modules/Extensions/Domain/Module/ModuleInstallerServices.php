<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use Doctrine\ORM\EntityManagerInterface;
use ForkCMS\Core\Domain\Doctrine\CreateSchema;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItemRepository;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupRepository;
use ForkCMS\Modules\Internationalisation\Domain\Importer\Importer;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ModuleInstallerServices
{
    public function __construct(
        public readonly CreateSchema $createSchema,
        public readonly ModuleRepository $moduleRepository,
        public readonly NavigationItemRepository $navigationRepository,
        public readonly UserGroupRepository $userGroupRepository,
        public readonly TranslationRepository $translationRepository,
        public readonly InstalledLocaleRepository $installedLocaleRepository,
        public readonly Importer $importer,
        public readonly TokenStorageInterface $tokenStorage,
        public readonly EntityManagerInterface $entityManager,
        public readonly MessageBusInterface $commandBus,
        public readonly ModuleSettings $moduleSettings,
        public readonly TranslatorInterface $translator
    ) {
    }
}
