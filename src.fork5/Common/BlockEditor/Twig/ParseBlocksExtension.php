<?php

namespace Common\BlockEditor\Twig;

use Common\BlockEditor\Blocks\AbstractBlock;
use Common\BlockEditor\Blocks\ParagraphBlock;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ParseBlocksExtension extends AbstractExtension
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'parse_blocks',
                [$this, 'parseBlocks'],
                ['needs_environment' => false, 'needs_context' => false, 'is_safe' => ['all']]
            ),
        ];
    }

    public function parseBlocks(string $json): string
    {
        $data = json_decode($json, true);

        if ($data === false || !is_array($data) || !array_key_exists('blocks', $data) || !array_key_exists('time', $data)) {
            return $json;
        }

        /** @var AdapterInterface $cache */
        $cache = $this->container->get('cache.block_editor');
        $cacheKey = md5($json);
        $cachedBlock = $cache->getItem($cacheKey);
        if ($cachedBlock->isHit()) {
            $parsedData = $cachedBlock->get();
            if ($parsedData['time'] === $data['time'] && $parsedData['version'] === $data['version']) {
                $cachedBlock->expiresAfter(null); // extend with default lifetime
                $cache->save($cachedBlock);

                return $parsedData['content'];
            }
        }

        $data['content'] = '';
        foreach ($data['blocks'] as $block) {
            $blockParserFQCN = $block['type'];
            if ($blockParserFQCN === 'paragraph') {
                $blockParserFQCN = ParagraphBlock::class;
            }

            if (!$this->container->has($blockParserFQCN)) {
                continue;
            }
            $blockParser = $this->container->get($blockParserFQCN);
            if (!$blockParser instanceof AbstractBlock) {
                continue;
            }

            $data['content'] .= $blockParser->parse($block['data']) . PHP_EOL;
        }

        $cachedBlock->set($data);
        $cache->save($cachedBlock);

        return $data['content'];
    }
}
