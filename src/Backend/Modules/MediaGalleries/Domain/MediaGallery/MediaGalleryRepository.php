<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Exception\MediaGalleryNotFound;

/**
 * @method MediaGallery|null findOneByTitle(string $title)
 */
final class MediaGalleryRepository extends EntityRepository
{
    /**
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function add(MediaGallery $mediaGallery): void
    {
        $this->getEntityManager()->persist($mediaGallery);
    }

    public function existsByTitle(string $title, string $ignoreMediaGalleryId = null): bool
    {
        $mediaGallery = $this->findOneByTitle($title);

        return ($mediaGallery instanceof MediaGallery) ? ($mediaGallery->getId() !== $ignoreMediaGalleryId) : false;
    }

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
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function remove(MediaGallery $mediaGallery): void
    {
        $this->getEntityManager()->remove($mediaGallery);
    }
}
