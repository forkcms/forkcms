<?php

namespace ForkCMS\Modules\Extensions\Domain\Theme\Command;

use ForkCMS\Core\Domain\Kernel\Event\ClearCacheEvent;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\Theme\Event\ThemeActivatedEvent;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ActivateThemeHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ThemeRepository $themeRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(ActivateTheme $activateTheme): void
    {
        $this->themeRepository->activateTheme($activateTheme->theme);
        $this->eventDispatcher->dispatch(new ThemeActivatedEvent($activateTheme->theme));
        $this->eventDispatcher->dispatch(new ClearCacheEvent());
    }
}
