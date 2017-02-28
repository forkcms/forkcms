<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;

final class MediaFolderRepository extends EntityRepository
{
    /**
     * Add a MediaFolder
     *
     * @param MediaFolder $mediaFolder
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function add(MediaFolder $mediaFolder)
    {
        $this->getEntityManager()->persist($mediaFolder);
    }

    /**
     * Does a folder exists by name?
     *
     * @param string $name The requested folder name to check if exists.
     * @param MediaFolder|null $parent The parent MediaFolder where this folder should be in.
     * @return boolean
     */
    public function existsByName(string $name, MediaFolder $parent = null): bool
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->findOneBy([
            'name' => (string) $name,
            'parent' => $parent,
        ]);

        return ($mediaFolder !== null);
    }

    /**
     * @param int|null $id
     * @return MediaFolder
     * @throws \Exception
     */
    public function getOneById(int $id): MediaFolder
    {
        if ($id === null) {
            throw MediaFolderNotFound::forEmptyId();
        }

        $mediaFolder = $this->findOneById((int) $id);

        if ($mediaFolder === null) {
            throw MediaFolderNotFound::forId($id);
        }

        return $mediaFolder;
    }

    /**
     * @param MediaFolder $mediaFolder
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function remove(MediaFolder $mediaFolder)
    {
        $this->getEntityManager()->remove($mediaFolder);
    }
}
