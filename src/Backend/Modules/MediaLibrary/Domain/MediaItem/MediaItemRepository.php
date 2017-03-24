<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;

final class MediaItemRepository extends EntityRepository
{
    /**
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
     * @return bool
     */
    public function existsOneByUrl(string $url): bool
    {
        /** @var MediaItem|null $mediaItem */
        $mediaItem = $this->findOneByUrl((string) $url);

        return $mediaItem !== null;
    }

    /**
     * @param string|null $id
     * @return MediaItem
     * @throws \Exception
     */
    public function findOneById(string $id = null): MediaItem
    {
        if ($id === null) {
            throw MediaItemNotFound::forEmptyId();
        }

        $mediaItem = parent::findOneById($id);

        if ($mediaItem === null) {
            throw MediaItemNotFound::forId($id);
        }

        return $mediaItem;
    }

    /**
     * @param MediaItem $mediaItem
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function remove(MediaItem $mediaItem)
    {
        $this->getEntityManager()->remove($mediaItem);
    }
}
