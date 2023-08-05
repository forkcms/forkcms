<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Event\TranslationCreatedEvent;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateTranslationHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TranslationRepository $translationRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateTranslation $createTranslation): void
    {
        $createTranslation->setEntity(Translation::fromDataTransferObject($createTranslation));
        $this->translationRepository->save($createTranslation->getEntity());
        $this->eventDispatcher->dispatch(new TranslationCreatedEvent($createTranslation->getEntity()));
    }
}
