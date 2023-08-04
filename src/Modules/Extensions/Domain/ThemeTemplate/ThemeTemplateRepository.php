<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ThemeTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThemeTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThemeTemplate[] findAll()
 * @method ThemeTemplate[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<ThemeTemplate>
 */
final class ThemeTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, ThemeTemplate::class);
    }

    public function save(ThemeTemplate $themeTemplate): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($themeTemplate);
        $entityManager->flush();
    }

    public function remove(ThemeTemplate $themeTemplate): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($themeTemplate);
        $entityManager->flush();
    }

    public function findDefaultTemplate(): ThemeTemplate
    {
        return $this->createQueryBuilder('tt')
            ->innerJoin('tt.theme', 't')
            ->where('t.active = :active AND tt = t.defaultTemplate')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleResult();
    }

    public function findActiveByName(string $name): ThemeTemplate
    {
        return $this->createQueryBuilder('tt')
            ->innerJoin('tt.theme', 't')
            ->where('t.active = :active AND tt.name = :name')
            ->setParameter('active', true)
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();
    }
}
