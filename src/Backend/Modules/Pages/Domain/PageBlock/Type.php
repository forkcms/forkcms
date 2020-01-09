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
    private const USER_TEMPLATE = 'usertemplate';

    public const POSSIBLE_TYPES = [
        self::RICH_TEXT,
        self::BLOCK,
        self::WIDGET,
        self::USER_TEMPLATE,
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

    public static function userTemplate(): self
    {
        return new self(self::USER_TEMPLATE);
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
        $userTemplate = self::userTemplate();

        return [
            $richText->getLabel() => $richText,
            $block->getLabel() => $block,
            $widget->getLabel() => $widget,
            $userTemplate->getLabel() => $userTemplate,
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
            case self::USER_TEMPLATE:
                return SpoonFilter::ucfirst(Language::lbl('UserTemplate'));
        }

        throw new RuntimeException('No label implemented for type: ' . $this->type);
    }
}
