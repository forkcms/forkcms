<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Event\ThemeTemplateChangedEvent;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChangeThemeTemplateHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ThemeTemplateRepository $themeTemplateRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(ChangeThemeTemplate $changeThemeTemplate): void
    {
        $themeTemplate = ThemeTemplate::fromDataTransferObject($changeThemeTemplate);
        $this->themeTemplateRepository->save($themeTemplate);
        $this->eventDispatcher->dispatch(ThemeTemplateChangedEvent::fromChangeCommand($changeThemeTemplate));
    }
}
