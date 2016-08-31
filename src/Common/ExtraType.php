<?php

namespace Common;

use Common\Exception\InvalidExtraType;

final class ExtraType
{
    const BLOCK = 'block';
    const HOMEPAGE = 'homepage';
    const WIDGET = 'widget';

    /** @var string */
    private $type;

    /**
     * @param string $type
     * @throws InvalidExtraType
     */
    public function __construct($type)
    {
        if (!in_array($type, self::getPossibleTypes())) {
            throw InvalidExtraType::withType($type);
        }

        $this->type = $type;
    }

    /**
     * @return array
     */
    public static function getPossibleTypes()
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
    public static function block()
    {
        return new self(self::BLOCK);
    }

    /**
     * @return self
     */
    public static function homepage()
    {
        return new self(self::HOMEPAGE);
    }

    /**
     * @return self
     */
    public static function widget()
    {
        return new self(self::WIDGET);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}
