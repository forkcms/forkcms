<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Core\Language\Language as BL;
use Common\Language;
use JsonSerializable;
use RuntimeException;
use SpoonFilter;

final class Type implements JsonSerializable
{
    private const RICH_TEXT = 'rich_text';
    private const BLOCK = 'block';
    private const WIDGET = 'widget';

    public const POSSIBLE_TYPES = [
        self::RICH_TEXT,
        self::BLOCK,
        self::WIDGET,
    ];

    /** @var string */
    private $type;

    /**
     * @param string $type
     *
     * @throws InvalidPageBlockTypeException
     */
    public function __construct(string $type)
    {
        if (!in_array($type, self::POSSIBLE_TYPES, true)) {
            throw InvalidPageBlockTypeException::withType($type);
        }

        $this->type = $type;
    }

    public function equals(self $type): bool
    {
        return $this->type === $type->type;
    }

    public function isRichText(): bool
    {
        return $this->equals(self::richText());
    }

    public function isBlock(): bool
    {
        return $this->equals(self::block());
    }

    public function isWidget(): bool
    {
        return $this->equals(self::widget());
    }

    public static function richText(): self
    {
        return new self(self::RICH_TEXT);
    }

    public static function block(): self
    {
        return new self(self::BLOCK);
    }

    public static function widget(): self
    {
        return new self(self::WIDGET);
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): string
    {
        return (string) $this;
    }

    /**
     * @return self[]
     */
    public static function dropdownChoices(): array
    {
        $richText = self::richText();
        $block = self::block();
        $widget = self::widget();

        return [
            $richText->getLabel() => $richText,
            $block->getLabel() => $block,
            $widget->getLabel() => $widget,
        ];
    }

    public function getLabel(): string
    {
        switch ($this->type) {
            case self::RICH_TEXT:
                return SpoonFilter::ucfirst(Language::lbl('Editor'));
            case self::BLOCK:
                return SpoonFilter::ucfirst(Language::lbl('Module'));
            case self::WIDGET:
                return SpoonFilter::ucfirst(Language::lbl('Widget'));
        }

        throw new RuntimeException('No label implemented for type: ' . $this->type);
    }
}
