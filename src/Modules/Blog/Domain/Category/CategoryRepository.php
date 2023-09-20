<?php

namespace ForkCMS\Modules\Blog\Domain\Category;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry) {
        parent::__construct($managerRegistry, Category::class);
    }

    public function save(Category $category): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($category);
        $entityManager->flush();
    }

    public function remove(Category $category): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($category);
        $entityManager->flush();
    }
}
