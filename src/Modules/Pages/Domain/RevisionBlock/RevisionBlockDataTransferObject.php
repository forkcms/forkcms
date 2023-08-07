<?php

namespace ForkCMS\Modules\Pages\Domain\RevisionBlock;

use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;

final class RevisionBlockDataTransferObject
{
    public ?Revision $revision;
    public ?string $position = null;
    public ?string $editorContent = null;
    public bool $isVisible = true;
    public ?int $sequence = null;
    /** @var array<string, mixed>  */
    public array $settings = [];
    public ?Block $block = null;

    public function __construct(?RevisionBlock $revisionBlock = null)
    {
        if ($revisionBlock === null) {
            return;
        }

        // dont map the revision since changes should result in a new revision
        $this->position = $revisionBlock->getPosition();
        $this->editorContent = $revisionBlock->getEditorContent();
        $this->isVisible = $revisionBlock->isVisible();
        $this->sequence = $revisionBlock->getSequence();
        $this->settings = $revisionBlock->getSettings()->all();
        $this->block = $revisionBlock->getBlock();
    }

    public function getType(): Type
    {
        return Type::fromBlockType($this->block?->getType());
    }
}
