<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Event\ThemeTemplateDeletedEvent;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteThemeTemplateHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ThemeTemplateRepository $themeTemplateRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(DeleteThemeTemplate $deleteThemeTemplate): void
    {
        $themeTemplate = $this->themeTemplateRepository->find($deleteThemeTemplate->id)
            ?? throw new InvalidArgumentException('Entity not found');
        $this->themeTemplateRepository->remove($themeTemplate);
        $deleteThemeTemplate->setEntity($themeTemplate);
        $this->eventDispatcher->dispatch(ThemeTemplateDeletedEvent::fromDeleteCommand($deleteThemeTemplate));
    }
}
