<?php

namespace Frontend\Core\Header;

use InvalidArgumentException;

final class MetaLink
{
    /** @var array */
    private $attributes;

    /** @var string */
    private $uniqueKey;

    /**
     * @param string $href since we always need a href we've added it as a separate parameter
     * @param string[] $attributes
     * @param string[] $uniqueAttributeKeys
     *
     * @throws InvalidArgumentException when the content is empty
     */
    public function __construct(
        string $href,
        array $attributes,
        array $uniqueAttributeKeys = ['rel', 'hreflang', 'type', 'title']
    ) {
        if (empty($href)) {
            throw new InvalidArgumentException('The href can not be empty');
        }

        $this->attributes = ['href' => $href] + $attributes;
        $this->createUniqueKey($uniqueAttributeKeys);
    }

    /**
     * @param string[] $uniqueAttributeKeys
     */
    private function createUniqueKey(array $uniqueAttributeKeys): void
    {
        // make sure the keys are sorted alphabetically
        sort($uniqueAttributeKeys);

        $this->uniqueKey = implode(
            '|',
            array_filter(
                array_map(
                    function (string $attributeKey) {
                        return $this->attributes[$attributeKey] ?? '';
                    },
                    $uniqueAttributeKeys
                )
            )
        );
    }

    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }

    public function hasAttributeWithValue(string $attributeKey, string $attributeValue): bool
    {
        return isset($this->attributes[$attributeKey]) && $this->attributes[$attributeKey] === $attributeValue;
    }

    public static function canonical(string $href): self
    {
        return new self(
            $href,
            ['rel' => 'canonical']
        );
    }

    public static function rss(string $href, string $title): self
    {
        return new self(
            $href,
            ['rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => $title]
        );
    }

    public static function alternateLanguage(string $href, string $language): self
    {
        return new self(
            $href,
            ['rel' => 'alternate', 'hreflang' => $language]
        );
    }

    public static function next(string $href): self
    {
        return new self(
            $href,
            ['rel' => 'next']
        );
    }

    public static function previous(string $href): self
    {
        return new self(
            $href,
            ['rel' => 'prev']
        );
    }

    public function __toString(): string
    {
        $html = '<link ';
        $html .= implode(
            ' ',
            array_map(
                function (string $parameterKey, string $parameterValue) {
                    return $parameterKey . '="' . $parameterValue . '"';
                },
                array_keys($this->attributes),
                $this->attributes
            )
        );
        $html .= '>';

        return $html;
    }
}
