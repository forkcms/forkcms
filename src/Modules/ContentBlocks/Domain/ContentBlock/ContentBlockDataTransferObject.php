<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

abstract class ContentBlockDataTransferObject
{
    protected ?ContentBlock $contentBlockEntity;

    public int $id;

    public Block $widget;

    public int $revisionId;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public string $title;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public string $template = ContentBlock::DEFAULT_TEMPLATE;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?string $text;

    public bool $isVisible = true;

    public Locale $locale;

    public Status $status;

    public SettingsBag $settings;

    public function __construct(?ContentBlock $contentBlockEntity = null)
    {
        $this->contentBlockEntity = $contentBlockEntity;

        if (!$contentBlockEntity instanceof ContentBlock) {
            $this->settings = new SettingsBag();

            return;
        }

        $this->id = $this->contentBlockEntity->getId();
        $this->widget = $this->contentBlockEntity->getWidget();
        $this->isVisible = !$this->contentBlockEntity->isHidden();
        $this->title = $this->contentBlockEntity->getTitle();
        $this->text = $this->contentBlockEntity->getText();
        $this->template = $this->contentBlockEntity->getTemplate();
        $this->locale = $this->contentBlockEntity->getLocale();
        $this->status = $this->contentBlockEntity->getStatus();
        $this->revisionId = $this->contentBlockEntity->getRevisionId();
    }

    public function getEntity(): ?ContentBlock
    {
        return $this->contentBlockEntity;
    }
}
