<?php

namespace ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Backend\Core\Engine\Model;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Common\ModuleExtraType;

final class CreateContentBlockHandler
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    public function handle(CreateContentBlock $createContentBlock): void
    {
        $createContentBlock->extraId = $this->getNewExtraId();
        $createContentBlock->id = $this->contentBlockRepository->getNextIdForLanguage($createContentBlock->locale);

        $contentBlock = ContentBlock::fromDataTransferObject($createContentBlock);
        $this->contentBlockRepository->add($contentBlock);

        $createContentBlock->setContentBlockEntity($contentBlock);
    }

    private function getNewExtraId(): int
    {
        return Model::insertExtra(
            ModuleExtraType::widget(),
            'ContentBlocks',
            'Detail'
        );
    }
}
