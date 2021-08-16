<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception\MediaGroupNotFound;

/**
 * @method MediaGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaGroup[]    findAll()
 * @method MediaGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MediaGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaGroup::class);
    }

    public function add(MediaGroup $mediaGroup)
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($mediaGroup);
    }

    public function findOneById(string $id = null): MediaGroup
    {
        if ($id === null) {
            throw MediaGroupNotFound::forEmptyId();
        }

        /** @var MediaGroup $mediaGroup */
        $mediaGroup = $this->find($id);

        if ($mediaGroup === null) {
            throw MediaGroupNotFound::forId($id);
        }

        return $mediaGroup;
    }
}
