<?php

namespace Common\BlockEditor\Blocks;

use Frontend\Core\Engine\TwigTemplate;

abstract class EditorBlock
{
    /** @var TwigTemplate */
    private $template;

    public function __construct(TwigTemplate $template)
    {
        $this->template = $template;
    }

    final public function getName(): string
    {
        return static::class;
    }

    /**
     * @return array The config must contain the key "class" with the JS class for the editor
     */
    abstract public function getConfig(): array;

    /**
     * @see https://github.com/editor-js/editorjs-php#configuration-file
     */
    abstract public function getValidation(): array;

    /** The url to the JS file with the config needed to make this block work in the editor */
    abstract public function getJavaScriptUrl(): ?string;

    abstract public function parse(array $data): string;
}
