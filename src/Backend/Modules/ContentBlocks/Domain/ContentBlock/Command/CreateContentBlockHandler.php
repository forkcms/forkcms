<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use App\Component\Model\BackendModel;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use App\Domain\ModuleExtra\Type;

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
        return BackendModel::insertExtra(
            Type::widget(),
            'ContentBlocks',
            'Detail'
        );
    }
}
