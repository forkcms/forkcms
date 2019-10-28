<?php

namespace Common\BlockEditor\Blocks;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EditorBlock
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    final public function getName(): string
    {
        $class = static::class;

        if ($class === Paragraph::class) {
            return 'paragraph'; // we can't get around this exception
        }

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
