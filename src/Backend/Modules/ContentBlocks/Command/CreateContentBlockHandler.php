<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Common\ModuleExtraType;

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
        $createContentBlock->contentBlock = ContentBlock::create(
            $this->contentBlockRepository->getNextIdForLanguage($createContentBlock->language),
            $createContentBlock->userId,
            $this->getNewExtraId(),
            $createContentBlock->language,
            $createContentBlock->title,
            $createContentBlock->text,
            !$createContentBlock->isVisible,
            $createContentBlock->template
        );

        $this->contentBlockRepository->add($createContentBlock->contentBlock);
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
