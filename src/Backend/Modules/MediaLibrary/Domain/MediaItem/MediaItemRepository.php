<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MediaItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaItem[]    findAll()
 * @method MediaItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method MediaItem|null findOneByUrl(string $url)
 */
final class MediaItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaItem::class);
    }

    public function add(MediaItem $mediaItem): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($mediaItem);
    }

    public function existsOneByUrl(string $url): bool
    {
        /** @var MediaItem|null $mediaItem */
        $mediaItem = $this->findOneByUrl($url);

        return $mediaItem !== null;
    }

    public function findOneById(string $id = null): MediaItem
    {
        if ($id === null) {
            throw MediaItemNotFound::forEmptyId();
        }

        $mediaItem = parent::find($id);

        if ($mediaItem === null) {
            throw MediaItemNotFound::forId($id);
        }

        return $mediaItem;
    }

    public function remove(MediaItem $mediaItem): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->remove($mediaItem);
    }

    public function findByFolderAndAspectRatio(MediaFolder $mediaFolder, ?AspectRatio $aspectRatio): array
    {
        $condition = ['folder' => $mediaFolder];

        if ($aspectRatio instanceof AspectRatio) {
            $condition['aspectRatio'] = $aspectRatio;
        }

        return $this->findBy(
            $condition,
            ['title' => 'ASC']
        );
    }

    public function findByFolderAndAspectRatioAndSearchQuery(
        MediaFolder $mediaFolder,
        ?AspectRatio $aspectRatio,
        ?string $searchQuery
    ): array {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('i');

        $queryBuilder = $queryBuilder->where('i.folder = :folder')
            ->orderBy('i.title', 'ASC')
            ->setParameter('folder', $mediaFolder);

        if ($aspectRatio instanceof AspectRatio) {
            $queryBuilder = $queryBuilder->andWhere('i.aspectRatio = :aspectRatio')
                ->setParameter('aspectRatio', $aspectRatio);
        }

        if ($searchQuery) {
            $queryBuilder = $queryBuilder->andWhere('i.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%'. $searchQuery .'%');
        }

        return $queryBuilder->getQuery()
            ->execute();
    }
}
