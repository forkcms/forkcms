<?php

namespace ForkCMS\Modules\Pages\Domain\Revision;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlockDataTransferObject;

abstract class RevisionDataTransferObject
{
    public ?Page $page = null;
    public ?Page $parentPage = null;
    public MenuType $type = MenuType::ROOT;
    public ?string $title = null;
    public bool $isDraft = false;
    public ?ThemeTemplate $themeTemplate = null;
    public DateTimeImmutable|null $isArchived = null;
    /** @var ArrayCollection<array-key, RevisionBlockDataTransferObject[]> */
    public ArrayCollection $blocks;
    public ?Locale $locale = null;
    /** @var array<string, mixed>  */
    public array $settings = [];
    public ?Meta $meta = null;
    protected ?Revision $revisionEntity;

    public function __construct(?Revision $revisionEntity = null)
    {
        $this->revisionEntity = $revisionEntity;
        $this->blocks = new ArrayCollection();

        if ($this->revisionEntity === null) {
            return;
        }

        $this->page = $this->revisionEntity->getPage();
        $this->parentPage = $this->revisionEntity->getParentPage();
        $this->type = $this->revisionEntity->getType();
        $this->title = $this->revisionEntity->getTitle();
        $this->isDraft = $this->revisionEntity->isDraft();
        $this->themeTemplate = $this->revisionEntity->getThemeTemplate();
        $this->isArchived = $this->revisionEntity->getArchivedDate();
        foreach ($this->revisionEntity->getBlocks() as $block) {
            $this->blocks->containsKey($block->getPosition())
                ? $this->blocks->get($block->getPosition())[] = new RevisionBlockDataTransferObject($block)
                : $this->blocks->set($block->getPosition(), [new RevisionBlockDataTransferObject($block)]);
        }
        $this->locale = $this->revisionEntity->getLocale();
        $this->settings = $this->revisionEntity->getSettings()->all();
        $this->meta = clone $this->revisionEntity->getMeta();
    }

    public function hasEntity(): bool
    {
        return $this->revisionEntity !== null;
    }

    public function getEntity(): Revision
    {
        return $this->revisionEntity;
    }

    public function addBlock(string $position, Block $block): void
    {
        $revisionBlock = new RevisionBlockDataTransferObject();
        $revisionBlock->block = $block;
        $revisionBlock->position = $position;
        $blocks = $this->blocks->get($position) ?? [];
        $blocks[] = $revisionBlock;
        $this->blocks->set($position, $blocks);
    }
}
