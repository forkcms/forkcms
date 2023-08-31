<?php

use ForkCMS\Core\Domain\Maker\Util\Entity;

/**
* @var string $class_name
* @var string $namespace
* @var Entity $entity
*/
?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
* @method <?= $entity->getName() ?>|null find($id, $lockMode = null, $lockVersion = null)
* @method <?= $entity->getName() ?>|null findOneBy(array $criteria, array $orderBy = null)
* @method <?= $entity->getName() ?>[] findAll()
* @method <?= $entity->getName() ?>[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
final class <?= $class_name ?> extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, <?= $entity->getName() ?>::class);
    }

    public function save(<?= $entity->getName() ?> $<?= lcfirst($entity->getName()) ?>): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($<?= lcfirst($entity->getName()) ?>);
        $entityManager->flush();
    }

    public function remove(<?= $entity->getName() ?> $<?= lcfirst($entity->getName()) ?>): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($<?= lcfirst($entity->getName()) ?>);
        $entityManager->flush();
    }
}
