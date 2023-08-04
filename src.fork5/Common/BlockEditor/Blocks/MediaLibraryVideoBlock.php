<?php

namespace Common\BlockEditor\Blocks;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class MediaLibraryVideoBlock extends AbstractBlock
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    public function __construct(MediaItemRepository $mediaItemRepository, ContainerInterface $container)
    {
        parent::__construct($container);

        $this->mediaItemRepository = $mediaItemRepository;
    }

    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.MediaLibraryVideo',
        ];
    }

    public function getValidation(): array
    {
        return [
            'id' => 'string',
            'videoId' => 'string',
        ];
    }

    public function parse(array $data): string
    {
        $data['mediaItem'] = $this->mediaItemRepository->find($data['id']);

        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/MediaLibraryVideoBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
