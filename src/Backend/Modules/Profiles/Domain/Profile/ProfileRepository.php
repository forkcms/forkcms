<?php

namespace Backend\Modules\Profiles\Domain\Profile;

use Backend\Core\Engine\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[] findAll()
 * @method Profile[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function add(Profile $profile): void
    {
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->flush();
    }

    public function existsByEmail(string $email, int $excludedProfileId = 0): bool
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.email = :email')
            ->setParameter(':email', $email);

        if ($excludedProfileId !== 0) {
            $query
                ->andWhere('p.id != :id')
                ->setParameter(':id', $excludedProfileId);
        }

        return $query->getQuery()->getOneOrNullResult() instanceof Profile;
    }

    public function existsByDisplayName(string $displayName, int $excludedProfileId = 0): bool
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.displayName = :displayName')
            ->setParameter(':displayName', $displayName);

        if ($excludedProfileId !== 0) {
            $query
                ->andWhere('p.id != :id')
                ->setParameter(':id', $excludedProfileId);
        }

        return $query->getQuery()->getOneOrNullResult() instanceof Profile;
    }

    public function getUrl(string $url, int $id = null): string
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $query = $queryBuilder
            ->select($queryBuilder->expr()->count('p.id'))
            ->where('p.url = :url')
            ->setParameter(':url', $url);

        if ($id !== null) {
            $query
                ->andWhere('p.id != :id')
                ->setParameter(':id', $id);
        }

        if ((int) $query->getQuery()->getSingleScalarResult() === 0) {
            return $url;
        }

        return $this->getUrl(Model::addNumber($url), $id);
    }
}
