<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Filter\FilteredTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Filter\TranslationFilter;
use Throwable;

/**
 * @method Translation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Translation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Translation[] findAll()
 * @method Translation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Translation>
 */
final class TranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ManagerRegistry $managerRegistry)
    {
        try {
            parent::__construct($registry, Translation::class);
        } catch (Throwable $throwable) {
            if (!empty($_ENV['FORK_DATABASE_HOST']) && $_ENV['APP_ENV'] !== 'test') {
                throw $throwable; // needed during the installer
            }
        }
    }

    public function remove(Translation $translation): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($translation);
        $entityManager->flush();
    }

    public function save(Translation ...$translations): void
    {
        $entityManager = $this->getEntityManager();
        foreach ($translations as $translation) {
            $entityManager->persist($translation);
        }

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $this->managerRegistry->resetManager();
            throw $uniqueConstraintViolationException;
        }
    }

    /**
     * @param array{domain:TranslationDomain, key:TranslationKey, locale:Locale} $fields
     *
     * @return Translation[]
     */
    public function uniqueDataTransferObjectMethod(array $fields): array
    {
        return $this->findBy(
            [
                'domain.application' => $fields['domain']->getApplication()->value,
                'domain.moduleName' => $fields['domain']->getModuleName(),
                'key.name' => $fields['key']->getName(),
                'key.type' => $fields['key']->getType()->value,
                'locale' => $fields['locale']->value,
            ]
        );
    }

    public function getTranslationsQueryBuilderForFilter(TranslationFilter $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t');

        if (!$filter->shouldFilter()) {
            return $queryBuilder;
        }

        if ($filter->application !== null) {
            $queryBuilder
                ->andWhere('t.domain.application = :application')
                ->setParameter('application', $filter->application->value);
        } else {
            $queryBuilder
                ->andWhere('t.domain.application in (:applications)')
                ->setParameter(
                    'applications',
                    array_map(
                        static fn (Application $application): string => $application->value,
                        array_filter(
                            Application::cases(),
                            static fn (Application $application): bool => $application->hasEditableTranslations()
                        )
                    )
                );
        }
        if ($filter->moduleName !== null) {
            if ($filter->moduleName === ModuleName::core()) {
                $queryBuilder->andWhere('t.domain.moduleName IS NULL');
            } else {
                $queryBuilder
                    ->andWhere('t.domain.moduleName = :moduleName')
                    ->setParameter('moduleName', $filter->moduleName);
            }
        }
        if (count($filter->type) > 0) {
            $queryBuilder
                ->andWhere('t.key.type IN (:types)')
                ->setParameter(
                    'types',
                    array_map(static fn (Type $type): string => $type->value, $filter->type)
                );
        }
        if (count($filter->locale) > 0) {
            $queryBuilder
                ->andWhere('t.locale IN (:locales)')
                ->setParameter(
                    'locales',
                    array_map(static fn (Locale $locale): string => $locale->value, $filter->locale)
                );
        }
        if ($filter->name !== null) {
            $queryBuilder
                ->andWhere('t.key.name LIKE :name')
                ->setParameter('name', '%' . $filter->name . '%');
        }
        if ($filter->value !== null) {
            $valueQueryBuilder = clone $queryBuilder;
            $matchingGroupIds = $valueQueryBuilder
                ->select('DISTINCT t.groupId')
                ->andWhere('t.value LIKE :value')
                ->setParameter('value', '%' . $filter->value . '%')
                ->getQuery()
                ->getSingleColumnResult();
            $queryBuilder->andWhere('t.groupId IN (:groupIds)')->setParameter('groupIds', $matchingGroupIds);
        }

        return $queryBuilder;
    }

    /** @return array<string, FilteredTranslation[]> */
    public function getFilteredTranslations(TranslationFilter $filter): array
    {
        if (!$filter->shouldFilter()) {
            return [];
        }

        /** @var array<string, FilteredTranslation[]> $filteredTranslations */
        $filteredTranslations = [];
        foreach ($this->getTranslationsQueryBuilderForFilter($filter)->getQuery()->toIterable() as $translation) {
            $key = $translation->getDomain() . '.' . $translation->getKey()->getName();
            $type = $translation->getKey()->getType()->value;

            if (!isset($filteredTranslations[$type][$key])) {
                $filteredTranslations[$type][$key] = FilteredTranslation::forTranslation($translation);
            }
            $filteredTranslations[$type][$key]->addTranslation($translation);
        }

        return $filteredTranslations;
    }
}
