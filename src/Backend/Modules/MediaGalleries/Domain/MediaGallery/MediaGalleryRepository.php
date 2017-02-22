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
     * Add a MediaGallery
     *
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
     * @param integer $ignoreMediaGalleryId
     * @return boolean
     */
    public function existsByTitle($title, $ignoreMediaGalleryId = 0)
    {
        /** @var MediaGallery $mediaGallery */
        $mediaGallery = $this->findOneByTitle($title);

        return ($mediaGallery)
            ? ($mediaGallery->getId() != $ignoreMediaGalleryId) : false;
    }

    /**
     * @param string|null $id
     * @return MediaGallery
     * @throws \Exception
     */
    public function getOneById($id)
    {
        if ($id === null) {
            throw MediaGalleryNotFound::forEmptyId();
        }

        /** @var MediaGallery|null $mediaGallery */
        $mediaGallery = $this->findOneById($id);

        if ($mediaGallery === null) {
            throw MediaGalleryNotFound::forId($id);
        }

        return $mediaGallery;
    }

    /**
     * Remove a MediaGallery
     *
     * @param MediaGallery $mediaGallery
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function remove(MediaGallery $mediaGallery)
    {
        $this->getEntityManager()->remove($mediaGallery);
    }
}
