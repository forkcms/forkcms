<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Event\TranslationDeletedEvent;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteTranslationHandler implements CommandHandlerInterface
{
    public function __construct(
        private TranslationRepository $translationRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(DeleteTranslation $deleteTranslation): void
    {
        $translation = $this->translationRepository->find($deleteTranslation->getTranslationId());
        if ($translation === null) {
            throw new InvalidArgumentException(
                'The translation with id ' . $deleteTranslation->getTranslationId() . ' does not exist'
            );
        }
        $this->translationRepository->remove($translation);
        $this->eventDispatcher->dispatch(new TranslationDeletedEvent($translation));
    }
}
