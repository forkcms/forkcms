<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

final class PageBlockType
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
     * @throws InvalidPageBlockType
     */
    public function __construct(string $type)
    {
        if (!in_array($type, self::POSSIBLE_TYPES, true)) {
            throw InvalidPageBlockType::withType($type);
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
}
