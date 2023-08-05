<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Command;

use ForkCMS\Core\Domain\Kernel\Event\ClearCacheEvent;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Event\TranslationChangedEvent;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChangeTranslationHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TranslationRepository $translationRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ChangeTranslation $changeTranslation): void
    {
        $this->translationRepository->save(Translation::fromDataTransferObject($changeTranslation));
        $this->eventDispatcher->dispatch(new TranslationChangedEvent($changeTranslation->getEntity()));
        $this->eventDispatcher->dispatch(new ClearCacheEvent());
    }
}
