<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Exception\MediaGalleryNotFound;

/**
 * MediaGallery Repository
 */
final class MediaGalleryRepository extends EntityRepository
{
    /**
     * @param MediaGallery $mediaGallery
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function add(MediaGallery $mediaGallery)
    {
        $this->getEntityManager()->persist($mediaGallery);
    }

    /**
     * Exists MediaGallery by title
     *
     * @param string $title
     * @param int $ignoreMediaGalleryId
     * @return bool
     */
    public function existsByTitle(string $title, int $ignoreMediaGalleryId = 0): bool
    {
        /** @var MediaGallery $mediaGallery */
        $mediaGallery = $this->findOneByTitle($title);

        return ($mediaGallery instanceof MediaGallery) ? ($mediaGallery->getId() !== $ignoreMediaGalleryId) : false;
    }

    /**
     * @param string|null $id
     * @return MediaGallery
     * @throws \Exception
     */
    public function findOneById(string $id = null): MediaGallery
    {
        if ($id === null) {
            throw MediaGalleryNotFound::forEmptyId();
        }

        /** @var MediaGallery|null $mediaGallery */
        $mediaGallery = parent::findOneById($id);

        if ($mediaGallery === null) {
            throw MediaGalleryNotFound::forId($id);
        }

        return $mediaGallery;
    }

    /**
     * @param MediaGallery $mediaGallery
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function remove(MediaGallery $mediaGallery)
    {
        $this->getEntityManager()->remove($mediaGallery);
    }
}
