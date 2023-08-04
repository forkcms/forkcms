<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

/**
 * @method InstalledLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstalledLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstalledLocale[] findAll()
 * @method InstalledLocale[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<InstalledLocale>
 */
final class InstalledLocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        try {
            parent::__construct($registry, InstalledLocale::class);
        } catch (Throwable $throwable) {
            if (!empty($_ENV['FORK_DATABASE_HOST']) && $_ENV['APP_ENV'] !== 'test') {
                throw $throwable; // needed during the installer
            }
        }
    }

    public function remove(InstalledLocale $installedLocale): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($installedLocale);
        $entityManager->flush();
    }

    public function save(InstalledLocale $installedLocale): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($installedLocale);
        $entityManager->flush();
    }

    /** @return array<string, InstalledLocale> */
    public function findAllIndexed(): array
    {
        return $this->createQueryBuilder('l', 'l.locale')->getQuery()->getResult();
    }

    /** @return array<string, Locale> */
    public function findInstalledLocales(): array
    {
        return array_map(
            static fn (InstalledLocale $installedLocale): Locale => $installedLocale->getLocale(),
            $this->findAllIndexed()
        );
    }

    /** @return string[] */
    public function findRedirectLocales(): array
    {
        return $this->createQueryBuilder('l', 'l.locale')
            ->select('l.locale')
            ->andWhere('l.isEnabledForBrowserLocaleRedirect = :isEnabledForBrowserLocaleRedirect')
            ->setParameter('isEnabledForBrowserLocaleRedirect', true)
            ->orderBy('l.isDefaultForWebsite', 'DESC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findDefaultWebsiteLocale(): string
    {
        return $this->createQueryBuilder('l', 'l.locale')
            ->select('l.locale')
            ->andWhere('l.isDefaultForWebsite = :isDefaultForWebsite')
            ->setParameter('isDefaultForWebsite', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /** @return string[] */
    public function findForWebsite(): array
    {
        $locales = $this->createQueryBuilder('l', 'l.locale')
            ->andWhere('l.isEnabledForWebsite = :isEnabledForWebsite')
            ->setParameter('isEnabledForWebsite', true)
            ->select('l.locale, l.isDefaultForWebsite')
            ->getQuery()
            ->getArrayResult()
        ;
        $websiteLocales = [];
        foreach ($locales as $locale) {
            $websiteLocales[$locale['locale']->value] = $locale['isDefaultForWebsite'];
        }

        return $websiteLocales;
    }
}
