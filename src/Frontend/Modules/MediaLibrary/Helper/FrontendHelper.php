<?php

namespace Frontend\Modules\MediaLibrary\Helper;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Frontend\Core\Header\Header;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Block\Widget as FrontendBlockWidget;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItemRepository;

/**
 * Frontend Helper
 * With this helper you can use the MediaLibrary Module easier then ever.
 */
class FrontendHelper
{
    /** @var MediaGroupMediaItemRepository */
    protected $mediaGroupMediaItemRepository;

    public function __construct(MediaGroupMediaItemRepository $mediaGroupMediaItemRepository)
    {
        $this->mediaGroupMediaItemRepository = $mediaGroupMediaItemRepository;
    }

    /**
     * Use this function for index pages,
     * where you only want to get the first media item for your entities.
     *
     * @param array $entities
     * @param string $methodForMediaGroup - F.e.: "getImagesMediaGroup"
     * @param string $newVariableName - F.e.: "image", this variable will be assigned in your entity
     * @param bool $onlyGetTheFirstMediaItem - true = only get the first item, false = get all items
     *
     * @throws \Exception
     */
    public function addMediaItemsToEntities(
        array $entities,
        string $methodForMediaGroup,
        string $newVariableName,
        bool $onlyGetTheFirstMediaItem = true
    ): void {
        // Init variables
        $mediaGroupIds = [];
        $entityKeys = [];
        $counter = 1;

        // Loop entities to get mediaGroup id
        foreach ($entities as $entityKey => $entity) {
            if ($entity === null) {
                unset($entities[$entityKey]);
                continue;
            }

            // Check if variable already exists or not
            if ($counter === 1) {
                if (property_exists($entities[$entityKey], $newVariableName)) {
                    throw new \Exception(
                        'The $newVariableName "' . $newVariableName . '" already exists, choose another name.'
                    );
                }
            }

            // Check if media group is not null
            if ($entity->{$methodForMediaGroup}() === null) {
                // skip this item
                continue;
            }

            // Define keys for later use
            $mediaGroupIds[(string) $entity->{$methodForMediaGroup}()->getId()] = $entity->getId();
            $entityKeys[$entity->getId()] = $entityKey;

            ++$counter;
        }

        // Define all MediaGroupMediaItem entities
        $mediaGroupMediaItems = $this->mediaGroupMediaItemRepository->getAll(
            array_keys($mediaGroupIds),
            (bool) $onlyGetTheFirstMediaItem
        );

        /** @var MediaGroupMediaItem $mediaGroupMediaItem */
        foreach ($mediaGroupMediaItems as $mediaGroupMediaItem) {
            $entityKey = $entityKeys[$mediaGroupIds[(string) $mediaGroupMediaItem->getGroup()->getId()]];
            $mediaItem = $mediaGroupMediaItem->getItem();

            if ($onlyGetTheFirstMediaItem) {
                // Define frontend media item
                $entities[$entityKey]->{$newVariableName} = $mediaItem;

                continue;
            }

            // Define frontend media item
            $entities[$entityKey]->{$newVariableName}[] = $mediaItem;
        }
    }

    /**
     * Add Open Graph Images for MediaGroup
     *
     * @param MediaGroup $mediaGroup
     * @param Header $header @todo: when we have a header in our services, use that one instead and remove this method variable
     * @param int $maximumItems Default is null, which means infinite images will be added to header
     */
    public function addOpenGraphImagesForMediaGroup(MediaGroup $mediaGroup, Header $header, int $maximumItems = 0): void
    {
        // Define variables
        $counter = 0;

        // Loop all connected items
        foreach ($mediaGroup->getConnectedItems() as $connectedItem) {
            if ($maximumItems !== 0 && $counter >= $maximumItems) {
                break;
            }

            if ($this->addOpenGraphImageForMediaItem($connectedItem->getItem(), $header)) {
                ++$counter;
            }
        }
    }

    /**
     * @param MediaItem $mediaItem
     * @param Header $header @todo: when we have a header in our services, use that one instead and remove this method variable
     *
     * @return bool
     */
    public function addOpenGraphImageForMediaItem(MediaItem $mediaItem, Header $header): bool
    {
        // Only image allowed
        if (!$mediaItem->getType()->isImage()) {
            return false;
        }

        $header->addOpenGraphImage(
            $mediaItem->getAbsoluteWebPath(),
            false,
            $mediaItem->getWidth(),
            $mediaItem->getHeight()
        );

        return true;
    }

    /**
     * Parse widget for a MediaGroupId in a custom module.
     *
     * Example:
     * $this->template->assign(
     *     'imagesWidget',
     *     // We can create widget for the MediaGroup id
     *     $this->get('media_library.helper.frontend')->parseWidget(
     *         'Lightbox',
     *         $this->blogArticle->getImageMediaGroup()->getId(),
     *         'My custom optional title',
     *         'CustomModule' // Optional field, if not given, defaults to "MediaLibrary"
     *     )
     * );
     *
     * @param string $mediaWidgetAction The ClassName from the Media widget you want to use.
     * @param string $mediaGroupId The MediaGroup id you want to parse
     * @param string $title You can give your optional custom title.
     * @param string $module You can parse a widget from a custom module. Default is the "MediaLibrary" module.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function parseWidget(
        string $mediaWidgetAction,
        string $mediaGroupId,
        string $title = null,
        string $module = null
    ) {
        if ($module === null) {
            $module = 'MediaLibrary';
        }

        // Create new widget instance and return parsed content
        $widget = new FrontendBlockWidget(
            FrontendModel::get('kernel'),
            $module,
            $mediaWidgetAction,
            serialize(
                [
                    'group_id' => $mediaGroupId,
                    'title' => $title,
                ]
            )
        );

        return $this->parseWidgetContent($widget);
    }

    private function parseWidgetContent(FrontendBlockWidget $widget): string
    {
        try {
            $widget->execute();

            return $widget->getContent();
        } catch (\Exception $e) {
            // if we are debugging, we want to see the exception
            if (FrontendModel::getContainer()->getParameter('kernel.debug')) {
                throw $e;
            }
        }
    }
}
