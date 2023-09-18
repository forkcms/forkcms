<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\Doctrine\CreateSchema;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Core\Installer\CoreInstaller;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\AjaxAction\ModuleAjaxAction;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItem;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItemRepository;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupRepository;
use ForkCMS\Modules\Backend\Domain\Widget\ModuleWidget;
use ForkCMS\Modules\Backend\Installer\BackendInstaller;
use ForkCMS\Modules\Extensions\Backend\Actions\ModuleIndex;
use ForkCMS\Modules\Extensions\Installer\ExtensionsInstaller;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockName;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Frontend\Installer\FrontendInstaller;
use ForkCMS\Modules\Internationalisation\Domain\Importer\Importer;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDomain;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use ForkCMS\Modules\Internationalisation\Installer\InternationalisationInstaller;
use RuntimeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ModuleInstaller
{
    public const IS_REQUIRED = false;

    /** If the module should show up on a list of installed or installable modules */
    public const IS_VISIBLE_IN_OVERVIEW = true;

    protected readonly CreateSchema $createSchema;
    protected readonly ModuleRepository $moduleRepository;
    protected readonly NavigationItemRepository $navigationRepository;
    protected readonly UserGroupRepository $userGroupRepository;
    protected readonly TranslationRepository $translationRepository;
    protected readonly InstalledLocaleRepository $installedLocaleRepository;
    protected readonly Importer $importer;
    protected readonly TokenStorageInterface $tokenStorage;
    protected readonly EntityManagerInterface $entityManager;
    protected readonly MessageBusInterface $commandBus;

    private ?ModuleInformation $moduleInformation = null;

    /** @var array<string,ModuleName> */
    private array $moduleDependencies = [];

    /** @var array<string,ModuleName> */
    private ?array $defaultModuleDependencies = null;

    private readonly ModuleSettings $moduleSettings;
    private bool $moduleRegistered = false;

    private TranslatorInterface $translator;

    public function __construct(
        ModuleInstallerServices $moduleInstallerServices,
    ) {
        $this->createSchema = $moduleInstallerServices->createSchema;
        $this->moduleRepository = $moduleInstallerServices->moduleRepository;
        $this->navigationRepository = $moduleInstallerServices->navigationRepository;
        $this->userGroupRepository = $moduleInstallerServices->userGroupRepository;
        $this->translationRepository = $moduleInstallerServices->translationRepository;
        $this->installedLocaleRepository = $moduleInstallerServices->installedLocaleRepository;
        $this->importer = $moduleInstallerServices->importer;
        $this->tokenStorage = $moduleInstallerServices->tokenStorage;
        $this->entityManager = $moduleInstallerServices->entityManager;
        $this->commandBus = $moduleInstallerServices->commandBus;
        $this->moduleSettings = $moduleInstallerServices->moduleSettings;
        $this->translator = $moduleInstallerServices->translator;
    }

    final public static function getModuleName(): ModuleName
    {
        return ModuleName::fromFQCN(static::class);
    }

    /**
     * Use this method to perform the actions needed to install the module.
     */
    public function install(): void
    {
    }

    /**
     * Use this method to perform actions before the uninstalled module dependencies are installed.
     */
    public function preInstall(): void
    {
    }

    final public function registerModule(): void
    {
        $this->moduleRepository->save(Module::fromModuleName(static::getModuleName()));
        $this->moduleRegistered = true;
    }

    final protected function addModuleDependency(ModuleName $moduleName): void
    {
        $this->moduleDependencies[$moduleName->getName()] = $moduleName;
    }

    /** @return array<string,ModuleName> */
    final public function getModuleDependencies(): array
    {
        return array_merge($this->moduleDependencies, $this->getDefaultModuleDependencies());
    }

    final protected function createTableForEntities(string ...$entityClasses): void
    {
        $this->createSchema->forEntityClasses(...$entityClasses);
    }

    /** @return array<string,ModuleName> */
    private function getDefaultModuleDependencies(): array
    {
        if ($this->defaultModuleDependencies !== null) {
            return $this->defaultModuleDependencies;
        }
        $this->defaultModuleDependencies = [];

        $coreModule = CoreInstaller::getModuleName();
        if ($coreModule === static::getModuleName()) {
            return $this->defaultModuleDependencies;
        }
        $this->defaultModuleDependencies[$coreModule->getName()] = $coreModule;

        $backendModule = BackendInstaller::getModuleName();
        if ($backendModule === static::getModuleName()) {
            return $this->defaultModuleDependencies;
        }
        $this->defaultModuleDependencies[$backendModule->getName()] = $backendModule;

        $frontendModule = FrontendInstaller::getModuleName();
        if ($frontendModule === static::getModuleName()) {
            return $this->defaultModuleDependencies;
        }
        $this->defaultModuleDependencies[$frontendModule->getName()] = $frontendModule;

        $internationalisationModule = InternationalisationInstaller::getModuleName();
        if ($internationalisationModule === static::getModuleName()) {
            return $this->defaultModuleDependencies;
        }
        $this->defaultModuleDependencies[$internationalisationModule->getName()] = $internationalisationModule;

        $extensionModule = ExtensionsInstaller::getModuleName();
        if ($extensionModule === static::getModuleName()) {
            return $this->defaultModuleDependencies;
        }
        $this->defaultModuleDependencies[$extensionModule->getName()] = $extensionModule;

        return $this->defaultModuleDependencies;
    }

    /** @param array<ActionSlug> $selectedFor */
    final protected function getOrCreateBackendNavigationItem(
        TranslationKey $label,
        ?ActionSlug $slug = null,
        ?NavigationItem $parent = null,
        array $selectedFor = [],
        ?int $sequence = null,
        bool $visibleInNavigationMenu = true,
    ): NavigationItem {
        $navigationItem = $this->navigationRepository->findUnique(
            $label,
            $slug,
            $parent
        );
        if (!$navigationItem instanceof NavigationItem) {
            $navigationItem = new NavigationItem($label, $slug, $parent, $visibleInNavigationMenu, $sequence);
            $this->navigationRepository->save($navigationItem);
        }

        foreach ($selectedFor as $selectedForLabel => $selectedForSlug) {
            $this->getOrCreateBackendNavigationItem(
                TranslationKey::label($selectedForLabel),
                $selectedForSlug,
                $navigationItem,
                [],
                null,
                false
            );
        }

        if ($slug instanceof ActionSlug) {
            $this->allowGroupToAccessModuleAction($slug->asModuleAction());
        }

        return $navigationItem;
    }

    final protected function getOrCreateFrontendBlock(
        BlockName $name,
        ?TranslationKey $label = null,
        SettingsBag $settings = new SettingsBag(),
        bool $hidden = false,
        ?int $position = null,
        ModuleName $module = null,
        Locale $locale = null
    ): Block {
        $module = $module ?? static::getModuleName();
        $moduleBlock = new ModuleBlock($module, $name);
        /** @var BlockRepository $blockRepository */
        $blockRepository = $this->getRepository(Block::class);
        $block = $blockRepository->findUnique($moduleBlock, $settings);

        if ($block instanceof Block) {
            return $block;
        }

        $block = new Block($moduleBlock, $label, $settings, $hidden, $position, $locale);
        $blockRepository->save($block);

        return $block;
    }

    final protected function getModulesNavigationItem(): NavigationItem
    {
        return $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Modules'),
            null,
            null,
            [],
            4,
        );
    }

    final protected function getSettingsNavigationItem(): NavigationItem
    {
        return $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Settings'),
            null,
            null,
            [],
            999,
        );
    }

    final protected function getModuleSettingsNavigationItem(): NavigationItem
    {
        return $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Modules'),
            ModuleIndex::getActionSlug(),
            $this->getSettingsNavigationItem(),
            [],
            0,
        );
    }

    /**
     * @param UserGroup|null $userGroup Defaults to the admin user group
     */
    final protected function allowGroupToAccessModuleAction(
        ModuleAction $moduleAction,
        UserGroup $userGroup = null
    ): void {
        $userGroup = $userGroup ?? $this->userGroupRepository->getAdminUserGroup();
        $userGroup->addAction($moduleAction);
    }

    /**
     * @param UserGroup|null $userGroup Defaults to the admin user group
     */
    final protected function allowGroupToAccessModuleAjaxAction(
        ModuleAjaxAction $moduleAjaxAction,
        UserGroup $userGroup = null
    ): void {
        $userGroup = $userGroup ?? $this->userGroupRepository->getAdminUserGroup();
        $userGroup->addAjaxAxtion($moduleAjaxAction);
    }

    /**
     * @param UserGroup|null $userGroup Defaults to the admin user group
     */
    final protected function allowGroupToAccessModuleWidget(
        ModuleWidget $moduleWidget,
        UserGroup $userGroup = null
    ): void {
        $userGroup = $userGroup ?? $this->userGroupRepository->getAdminUserGroup();
        $userGroup->addWidget($moduleWidget);
    }

    final protected function setSetting(string $key, mixed $value, ModuleName $moduleName = null): void
    {
        if (!$this->moduleRegistered) {
            throw new RuntimeException('You cannot set module settings during the pre install phase');
        }

        $this->moduleSettings->set($moduleName ?? static::getModuleName(), $key, $value);
    }

    final protected function importTranslations(
        string $translationPath,
        bool $overwriteConflicts = false
    ): void {
        $this->importer->import($translationPath, $overwriteConflicts);
    }

    /** @param StampInterface[] $stamps */
    final protected function dispatchCommand(object $command, array $stamps = []): Envelope
    {
        return $this->commandBus->dispatch($command, $stamps);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $entityFQCN
     *
     * @return EntityRepository<T>
     */
    final protected function getRepository(string $entityFQCN): EntityRepository
    {
        return $this->entityManager->getRepository($entityFQCN);
    }

    final public function getInformation(): ModuleInformation
    {
        if ($this->moduleInformation === null) {
            $this->moduleInformation = ModuleInformation::fromModule(self::getModuleName());
        }

        return $this->moduleInformation;
    }

    public function isModuleRegistered(): bool
    {
        return $this->moduleRegistered;
    }

    /** @param array<string, mixed> $parameters */
    public function trans(Locale $locale, string $id, array $parameters = [], ?ModuleName $module = null): string
    {
        $module = $module ?? self::getModuleName();
        $domain = new TranslationDomain(Application::INSTALLER, $module);

        return $this->translator->trans($id, $parameters, $domain->getDomain(), $locale->value);
    }
}
