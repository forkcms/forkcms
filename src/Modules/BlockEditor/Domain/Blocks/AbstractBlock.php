<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

use ForkCMS\Core\Domain\Header\Asset\Asset;
use Twig\Environment;

abstract class AbstractBlock
{
    public function __construct(private readonly Environment $twig)
    {
    }

    final public function getName(): string
    {
        $class = static::class;

        if ($class === ParagraphBlock::class) {
            return 'paragraph'; // we can't get around this exception
        }

        return static::class;
    }

    /**
     * @return array<string, string> The config must contain the key "class" with the JS class for the editor
     */
    abstract public function getConfig(): array;

    /**
     * @see https://github.com/editor-js/editorjs-php#configuration-file
     *
     * @return array<string, mixed>
     */
    abstract public function getValidation(): array;

    /** The url to the JS file with the config needed to make this block work in the editor */
    public function getJavascript(): ?Asset
    {
        return null;
    }

    /** @param array<array-key, mixed> $data */
    abstract public function parse(array $data): string;

    /** @param array<array-key, mixed> $data */
    final protected function parseWithTwig(string $template, array $data): string
    {
        return $this->twig->render($template, ['editorBlock' => $data]);
    }
}
