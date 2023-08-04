<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

/**
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[] findAll()
 * @method Module[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Module>
 */
final class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        try {
            parent::__construct($registry, Module::class);
        } catch (Throwable $throwable) {
            if (!empty($_ENV['FORK_DATABASE_HOST']) && $_ENV['APP_ENV'] !== 'test') {
                throw $throwable;
            }
        }
    }

    public function save(Module $module): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($module);
        $entityManager->flush();
    }

    /**
     * @return array<string, Module>
     */
    public function findAllIndexed(): array
    {
        return $this->createQueryBuilder('m', 'm.name')->getQuery()->getResult();
    }
}
