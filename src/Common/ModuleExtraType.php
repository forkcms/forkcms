<?php

namespace Common;

use Common\Exception\InvalidModuleExtraType;

final class ModuleExtraType
{
    const BLOCK = 'block';
    const HOMEPAGE = 'homepage';
    const WIDGET = 'widget';

    /** @var string */
    private $type;

    /**
     * @param string $type
     * @throws InvalidModuleExtraType
     */
    public function __construct(string $type)
    {
        if (!in_array($type, self::getPossibleTypes())) {
            throw InvalidModuleExtraType::withType($type);
        }

        $this->type = $type;
    }

    /**
     * @return array
     */
    public static function getPossibleTypes(): array
    {
        $possibleTypes = [
            self::BLOCK,
            self::HOMEPAGE,
            self::WIDGET,
        ];

        return $possibleTypes;
    }

    /**
     * @return self
     */
    public static function block(): self
    {
        return new self(self::BLOCK);
    }

    /**
     * @return self
     */
    public static function homepage(): self
    {
        return new self(self::HOMEPAGE);
    }

    /**
     * @return self
     */
    public static function widget(): self
    {
        return new self(self::WIDGET);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}
