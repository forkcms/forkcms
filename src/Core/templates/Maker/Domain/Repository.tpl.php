<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
* @method <?= $entity ?>|null find($id, $lockMode = null, $lockVersion = null)
* @method <?= $entity ?>|null findOneBy(array $criteria, array $orderBy = null)
* @method <?= $entity ?>[] findAll()
* @method <?= $entity ?>[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
final class <?= $class_name ?> extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, <?= $entity ?>::class);
    }

    public function save(<?= $entity ?> $<?= lcfirst($entity) ?>): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($<?= lcfirst($entity) ?>);
        $entityManager->flush();
    }

    public function remove(<?= $entity ?> $<?= lcfirst($entity) ?>): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($<?= lcfirst($entity) ?>);
        $entityManager->flush();
    }
}
