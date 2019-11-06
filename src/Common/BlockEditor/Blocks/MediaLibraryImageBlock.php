<?php

namespace Common\BlockEditor\Blocks;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Frontend\Core\Engine\TwigTemplate;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class MediaLibraryImageBlock extends AbstractBlock
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    public function __construct(MediaItemRepository $mediaItemRepository, TwigTemplate $templating)
    {
        parent::__construct($templating);

        $this->mediaItemRepository = $mediaItemRepository;
    }

    public function getConfig(): array
    {
        return [
            'shortcut' => 'CMD+SHIFT+I',
            'class' => 'BlockEditor.blocks.MediaLibraryImage',
        ];
    }

    public function getValidation(): array
    {
        return [
            'id' => 'string',
            'src' => 'string',
        ];
    }

    public function parse(array $data): string
    {
        $data['mediaItem'] = $this->mediaItemRepository->find($data['id']);

        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/MediaLibraryImageBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return '/js/editor.js';
    }
}
