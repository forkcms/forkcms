<?php

namespace ForkCMS\Modules\Internationalisation\Domain\ModuleSettings\Command;

use ForkCMS\Core\Domain\Kernel\Event\ClearCacheEvent;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Event\InstalledLocaleChangedEvent;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Internationalisation\Domain\ModuleSettings\Event\ModuleSettingsChanged;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChangeModuleSettingsHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly InstalledLocaleRepository $installedLocaleRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ChangeModuleSettings $changeSettings): void
    {
        $changeSettings->validateDefaults();

        foreach ($changeSettings->installedLocales as $locale) {
            $locale->isDefaultForUser = $locale->locale === $changeSettings->defaultForUser;
            $locale->isDefaultForWebsite = $locale->locale === $changeSettings->defaultForWebsite;
            $locale->isEnabledForBrowserLocaleRedirect = $locale->isEnabledForBrowserLocaleRedirect
                && $locale->isEnabledForWebsite;

            $installedLocale = InstalledLocale::fromDataTransferObject($locale);
            $this->installedLocaleRepository->save($installedLocale);
            $this->eventDispatcher->dispatch(new InstalledLocaleChangedEvent($installedLocale));
        }

        $this->eventDispatcher->dispatch(new ClearCacheEvent());
    }
}
