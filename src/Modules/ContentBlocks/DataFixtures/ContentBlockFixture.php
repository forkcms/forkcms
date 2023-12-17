<?php

namespace ForkCMS\Modules\ContentBlocks\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Frontend\Widgets\ContentBlock as ContentBlockWidget;
use ForkCMS\Modules\Extensions\tests\ForkFixture;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class ContentBlockFixture extends ForkFixture
{
    public const CONTENT_BLOCK_VISIBLE_ID = 1;
    public const CONTENT_BLOCK_VISIBLE_TITLE = 'Visible content block';
    public const CONTENT_BLOCK_VISIBLE_TEXT = 'This is a visible content block';
    public const CONTENT_BLOCK_HIDDEN_ID = 2;
    public const CONTENT_BLOCK_HIDDEN_TITLE = 'Hidden content block';
    public const CONTENT_BLOCK_HIDDEN_TEXT = 'This is a hidden content block';

    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            $this->createContentBlock(
                self::CONTENT_BLOCK_VISIBLE_ID,
                self::CONTENT_BLOCK_VISIBLE_TITLE,
                self::CONTENT_BLOCK_VISIBLE_TEXT
            )
        );
        $manager->persist(
            $this->createContentBlock(
                self::CONTENT_BLOCK_HIDDEN_ID,
                self::CONTENT_BLOCK_HIDDEN_TITLE,
                self::CONTENT_BLOCK_HIDDEN_TEXT,
                false
            )
        );

        $manager->flush();
    }

    private function createContentBlock(
        int $id,
        string $title,
        string $text,
        bool $isVisible = true,
    ): ContentBlock {
        $newContentBlock = new CreateContentBlock(Locale::ENGLISH);
        $newContentBlock->title = $title;
        $newContentBlock->text = $text;
        $newContentBlock->isVisible = $isVisible;
        $newContentBlock->id = $id;
        $newContentBlock->widget = new Block(
            ModuleBlock::fromFQCN(ContentBlockWidget::class),
            TranslationKey::label('ContentBlocks'),
            locale: $newContentBlock->locale
        );

        return ContentBlock::fromDataTransferObject($newContentBlock);
    }
}
