<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Common\ExtraType;

final class CreateContentBlockHandler
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    /**
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    /**
     * @param CreateContentBlock $createContentBlock
     *
     * @return ContentBlock
     */
    public function handle(CreateContentBlock $createContentBlock)
    {
        $createContentBlock->contentBlock = ContentBlock::create(
            $this->contentBlockRepository->getNextIdForLanguage($createContentBlock->language),
            $this->getNewExtraId(),
            $createContentBlock->language,
            $createContentBlock->title,
            $createContentBlock->text,
            !$createContentBlock->isVisible,
            $createContentBlock->template
        );

        $this->contentBlockRepository->add($createContentBlock->contentBlock);
    }

    /**
     * @return int
     */
    private function getNewExtraId()
    {
        return Model::insertExtra(
            ExtraType::widget(),
            'ContentBlocks',
            'Detail'
        );
    }
}
