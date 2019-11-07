<?php

namespace Backend\Modules\Pages\Domain\Page;

final class Status
{
    public const ACTIVE = 'active';
    public const ARCHIVE = 'archive';
    public const DRAFT = 'draft';

    public const POSSIBLE_TYPES = [
        self::ACTIVE,
        self::ARCHIVE,
        self::DRAFT,
    ];

    /** @var string */
    private $type;

    /**
     * @param string $type
     *
     * @throws InvalidPageStatusException
     */
    public function __construct(string $type)
    {
        if (!in_array($type, self::POSSIBLE_TYPES, true)) {
            throw InvalidPageStatusException::withType($type);
        }

        $this->type = $type;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function archive(): self
    {
        return new self(self::ARCHIVE);
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
