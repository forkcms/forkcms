<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Editor;

use EditorJS\EditorJS;
use EditorJS\EditorJSException;
use ForkCMS\Core\Domain\Header\Asset\AssetCollection;
use ForkCMS\Modules\BlockEditor\Domain\Blocks\AbstractBlock;
use ForkCMS\Modules\BlockEditor\Domain\Blocks\ParagraphBlock;
use JsonException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class BlockEditorConfig
{
    /** @var array<string, array<string, string>> */
    private array $config = [];

    /** @var array<string, array<string, mixed>> */
    private array $validation = [];

    private AssetCollection $javascripts;

    /** @param ServiceLocator<AbstractBlock> $editorBlocks */
    public function __construct(
        #[TaggedLocator('fork.block_editor.block')]
        public readonly ServiceLocator $editorBlocks,
        private readonly bool $isDebug,
        private readonly CacheItemPoolInterface $cacheBlockEditor
    ) {
        $this->javascripts = new AssetCollection();
    }

    public function configureBlocks(): self
    {
        foreach ($this->editorBlocks->getProvidedServices() as $editorBlockClassName) {
            $editorBlock = $this->editorBlocks->get($editorBlockClassName);
            $this->config[$editorBlock->getName()] = $editorBlock->getConfig();
            $this->validation[$editorBlock->getName()] = $editorBlock->getValidation();
            $javascript = $editorBlock->getJavascript();
            if ($javascript !== null) {
                $this->javascripts->add($javascript);
            }
        }

        return $this;
    }

    /** @return array<string, array<string, string>> */
    public function getConfig(): array
    {
        if ($this->config === []) {
            $this->configureBlocks();
        }

        return $this->config;
    }

    public function isValid(string $json): bool
    {
        if ($this->config === []) {
            $this->configureBlocks();
        }

        try {
            new EditorJS($json, json_encode(['tools' => $this->validation], JSON_THROW_ON_ERROR));
        } catch (EditorJSException | JsonException $exception) {
            if ($this->isDebug) {
                throw $exception;
            }

            return false;
        }

        return true;
    }

    public function getJavascripts(): AssetCollection
    {
        if ($this->config === []) {
            $this->configureBlocks();
        }

        return $this->javascripts;
    }

    public function createHtmlFromJson(string $json): string
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (
            $data === false
            || !is_array($data)
            || !array_key_exists('blocks', $data)
            || !array_key_exists('time', $data)
        ) {
            return $json;
        }
        $cacheKey = md5($json);
        $cachedBlock = $this->cacheBlockEditor->getItem($cacheKey);
        if (!$this->isDebug && $cachedBlock->isHit()) {
            $parsedData = $cachedBlock->get();
            if ($parsedData['time'] === $data['time'] && $parsedData['version'] === $data['version']) {
                $cachedBlock->expiresAfter(null); // extend with default lifetime
                $this->cacheBlockEditor->save($cachedBlock);

                return $parsedData['content'];
            }
        }

        $data['content'] = '';
        foreach ($data['blocks'] as $block) {
            $blockParserFQCN = $block['type'];
            if ($blockParserFQCN === 'paragraph') {
                $blockParserFQCN = ParagraphBlock::class;
            }

            if (!$this->editorBlocks->has($blockParserFQCN)) {
                continue;
            }
            $blockParser = $this->editorBlocks->get($blockParserFQCN);
            if (!$blockParser instanceof AbstractBlock) {
                continue;
            }

            $data['content'] .= $blockParser->parse($block['data']) . PHP_EOL;
        }

        $cachedBlock->set($data);
        $this->cacheBlockEditor->save($cachedBlock);

        return $data['content'];
    }
}
