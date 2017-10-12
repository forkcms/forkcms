<?php

namespace Backend\Modules\SitemapGenerator\Domain;

use Doctrine\ORM\EntityRepository;

class SitemapEntryRepository extends EntityRepository
{
    public function add(SitemapEntry $sitemapEntry): void
    {
        $this->getEntityManager()->persist($sitemapEntry);
    }

    public function update(SitemapEntry $sitemapEntry): void
    {
        $this->getEntityManager()->flush($sitemapEntry);
    }

    public function getChildren(SitemapEntry $parent): array
    {
        return $this->createQueryBuilder('se')
            ->select('se')
            ->where('se.url LIKE :parentUrl')
            ->setParameter('parentUrl', $parent->getUrl() . '%')
            ->getQuery()
            ->getResult();
    }
}
