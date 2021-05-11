<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;

/**
 * @method MediaFolder|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaFolder|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaFolder[]    findAll()
 * @method MediaFolder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MediaFolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaFolder::class);
    }

    public function add(MediaFolder $mediaFolder): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($mediaFolder);
    }

    private function bumpFolderCount(int $folderId, array &$counts): void
    {
        // Counts for folder doesn't exist
        if (!array_key_exists($folderId, $counts)) {
            // Init counts
            $counts[$folderId] = 1;

            return;
        }

        // Bump counts
        ++$counts[$folderId];
    }

    /**
     * Does a folder exists by name?
     *
     * @param string $name The requested folder name to check if exists.
     * @param MediaFolder|null $parent The parent MediaFolder where this folder should be in.
     *
     * @return bool
     */
    public function existsByName(string $name, MediaFolder $parent = null): bool
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->findOneBy([
            'name' => $name,
            'parent' => $parent,
        ]);

        return $mediaFolder instanceof MediaFolder;
    }

    public function findDefault(): MediaFolder
    {
        return $this->findBy([], ['name' => 'ASC'], 1)[0];
    }

    public function findOneById(int $id = null): MediaFolder
    {
        if ($id === null) {
            throw MediaFolderNotFound::forEmptyId();
        }

        $mediaFolder = $this->find($id);

        if ($mediaFolder === null) {
            throw MediaFolderNotFound::forId($id);
        }

        return $mediaFolder;
    }

    public function getCountsForMediaGroup(MediaGroup $mediaGroup): array
    {
        // Init counts
        $counts = [];

        // Loop all connected items
        foreach ($mediaGroup->getConnectedItems() as $connectedItem) {
            $this->bumpFolderCount($connectedItem->getItem()->getFolder()->getId(), $counts);
        }

        return $counts;
    }

    public function remove(MediaFolder $mediaFolder): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->remove($mediaFolder);
    }
}
