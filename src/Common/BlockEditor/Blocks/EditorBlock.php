<?php

namespace Common\BlockEditor\Blocks;

use JsonSerializable;

abstract class EditorBlock implements JsonSerializable
{
    /** @var array */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    final public static function getName(): string
    {
        return static::class;
    }

    /**
     * @return array The config must contain the key "class" with the JS class for the editor
     */
    abstract public static function getConfig(): array;

    /**
     * @see https://github.com/editor-js/editorjs-php#configuration-file
     */
    abstract public static function getValidation(): array;

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
