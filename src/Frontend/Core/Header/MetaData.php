<?php

namespace Frontend\Core\Header;

use InvalidArgumentException;

final class MetaData
{
    /** @var string[] */
    private $attributes;

    /** @var string */
    private $uniqueKey;

    /**
     * @param string $content since we always need content we've added it as a separate parameter
     * @param string[] $attributes
     * @param string[] $uniqueAttributeKeys
     * @param string|null $uniqueKeySuffix
     *
     * @throws InvalidArgumentException when the content is empty
     */
    public function __construct(
        string $content,
        array $attributes,
        array $uniqueAttributeKeys = ['content'],
        string $uniqueKeySuffix = null
    ) {
        if (empty($content)) {
            throw new InvalidArgumentException('The content can not be empty');
        }

        $this->attributes = ['content' => $content] + $attributes;
        $this->createUniqueKey($uniqueAttributeKeys, $uniqueKeySuffix);
    }

    /**
     * @param string[] $uniqueAttributeKeys
     * @param string|null $uniqueKeySuffix
     */
    private function createUniqueKey(array $uniqueAttributeKeys, string $uniqueKeySuffix = null)
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

        if ($uniqueKeySuffix !== null) {
            $this->uniqueKey .= '|' . $uniqueKeySuffix;
        }
    }

    /**
     * @return string
     */
    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }

    /**
     * @param string $attributeKey
     * @param string $attributeValue
     *
     * @return bool
     */
    public function hasAttributeWithValue(string $attributeKey, string $attributeValue): bool
    {
        return isset($this->attributes[$attributeKey]) && $this->attributes[$attributeKey] === $attributeValue;
    }

    /**
     * Some things should be appended instead of ignored when the meta data is already set instead of ignored.
     *
     * @return bool
     */
    public function shouldMergeOnDuplicateKey()
    {
        return in_array($this, ['description', 'keywords', 'robots']);
    }

    /**
     * @param self $metaData
     */
    public function merge(self $metaData)
    {
        foreach ($metaData->attributes as $attributeKey => $attributeValue) {
            // the content should be appended, the rest of the attributes gets overwritten or added
            if ($attributeKey === 'content' && isset($this->attributes[$attributeKey])) {
                $this->attributes[$attributeKey] .= ', ' . $attributeValue;

                continue;
            }

            $this->attributes[$attributeKey] = $attributeValue;
        }
    }

    /**
     * @param string $name
     * @param string $content
     *
     * @return self
     */
    public static function forName(
        string $name,
        string $content
    ) {
        return new self($content, ['name' => $name], ['name']);
    }

    /**
     * @param string $property
     * @param string $content
     * @param array $uniqueAttributeKeys
     *
     * @return self
     */
    public static function forProperty(
        string $property,
        string $content,
        array $uniqueAttributeKeys = ['property']
    ) {
        return new self($content, ['property' => $property], $uniqueAttributeKeys);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $html = '<meta ';
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
