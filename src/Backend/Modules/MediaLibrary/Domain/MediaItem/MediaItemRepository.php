<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;

final class MediaItemRepository extends EntityRepository
{
    /**
     * Add a MediaItem
     *
     * @param MediaItem $mediaItem
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function add(MediaItem $mediaItem)
    {
        $this->getEntityManager()->persist($mediaItem);
    }

    /**
     * Exists one MediaItem by url
     *
     * @param string $url
     * @return boolean
     */
    public function existsOneByUrl(string $url): bool
    {
        /** @var MediaItem|null $mediaItem */
        $mediaItem = $this->findOneByUrl((string) $url);

        return ($mediaItem !== null);
    }

    /**
     * Get all MediaItem items by MediaFolder
     *
     * @param MediaFolder $mediaFolder
     * @return array
     */
    public function getAllByFolder(MediaFolder $mediaFolder): array
    {
        return $this->findBy(array(
            'folder' => $mediaFolder
        ));
    }

    /**
     * Get one by id
     *
     * @param string|null $id
     * @return MediaItem
     * @throws \Exception
     */
    public function getOneById(string $id): MediaItem
    {
        if ($id === null) {
            throw MediaItemNotFound::forEmptyId();
        }

        $mediaItem = $this->findOneById($id);

        if ($mediaItem === null) {
            throw MediaItemNotFound::forId($id);
        }

        return $mediaItem;
    }

    /**
     * Remove a MediaItem
     *
     * @param MediaItem $mediaItem
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function remove(MediaItem $mediaItem)
    {
        $this->getEntityManager()->remove($mediaItem);
    }
}
