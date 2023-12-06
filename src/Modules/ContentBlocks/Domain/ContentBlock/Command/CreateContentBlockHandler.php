<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockCreatedEvent;
use ForkCMS\Modules\ContentBlocks\Frontend\Widgets\Detail as DetailWidget;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class CreateContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private ContentBlockRepository $contentBlockRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateContentBlock $createContentBlock): void
    {
        $createContentBlock->widget = new Block(
            ModuleBlock::fromFQCN(DetailWidget::class),
            TranslationKey::label('ContentBlocks'),
            locale: $createContentBlock->locale
        );

        $createContentBlock->id = $this->contentBlockRepository->getNextIdForLocale($createContentBlock->locale);

        $contentBlock = ContentBlock::fromDataTransferObject($createContentBlock);
        $this->contentBlockRepository->save($contentBlock);
        $createContentBlock->setEntity($contentBlock);
        $this->eventDispatcher->dispatch(new ContentBlockCreatedEvent($contentBlock));
    }
}
