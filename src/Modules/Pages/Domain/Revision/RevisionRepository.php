<?php

namespace ForkCMS\Modules\Pages\Domain\Revision;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaCallbackService;
use ForkCMS\Modules\Frontend\Domain\Meta\RepositoryWithMetaTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlock;

/**
 * @method Revision|null find($id, $lockMode = null, $lockVersion = null)
 * @method Revision|null findOneBy(array $criteria, array $orderBy = null)
 * @method Revision[] findAll()
 * @method Revision[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Revision>
 */
final class RevisionRepository extends ServiceEntityRepository implements MetaCallbackService
{
    /** @phpstan-use RepositoryWithMetaTrait<Revision> */
    use RepositoryWithMetaTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly NavigationBuilder $navigationBuilder,
    ) {
        parent::__construct($managerRegistry, Revision::class);
    }

    public function save(Revision $revision): void
    {
        $entityManager = $this->getEntityManager();

        $revision->getMeta()->setSlug($this->slugify($revision->getTitle(), $revision, $revision->getLocale()));
        $entityManager->persist($revision);
        $entityManager->flush();
        if ($revision->getPage()->getId() === Page::PAGE_ID_HOME) {
            $revision->getMeta()->setSlug('');
            $entityManager->flush();
        }
        $this->navigationBuilder->clearNavigationCache();
    }

    public function remove(Revision $revision): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($revision);
        $entityManager->flush();
        $this->navigationBuilder->clearNavigationCache();
    }

    public function generateSlug(string $slug, Locale $locale, ?int $revisionId): string
    {
        if ($revisionId === null) {
            return $this->slugify($slug, null, $locale);
        }

        return $this->slugify($slug, $this->findOneBy(['id' => $revisionId, 'locale' => $locale->value]), $locale);
    }

    protected function slugifyIdQueryBuilder(
        QueryBuilder $queryBuilder,
        ?object $subject,
        Locale $locale,
        string $entityAlias
    ): void {
        $queryBuilder
            ->andWhere($entityAlias . '.locale = :locale')
            ->setParameter('locale', ($subject?->getLocale() ?? $locale)->value);
        if ($subject?->getPage()?->hasId() ?? false) {
            $queryBuilder
                ->andWhere($entityAlias . '.page != :page')
                ->setParameter('page', $subject->getPage());
        }
        if ($subject !== null) {
            if ($subject->getParentPage() === null) {
                $queryBuilder
                    ->andWhere($entityAlias . '.parentPage IS NULL');
            } else {
                $queryBuilder
                    ->andWhere($entityAlias . '.parentPage = :parentPage')
                    ->setParameter('parentPage', $subject->getParentPage());
            }
        }
    }

    public function removeFrontendBlockFromRevision(Block $block): void
    {
        $this->getEntityManager()->getFilters()->disable('softdeleteable');

        $revisions = $this->getEntityManager()->createQueryBuilder()
            ->from(Revision::class, 'r')
            ->select('r')
            ->innerJoin('r.blocks', 'rb')
            ->innerJoin('rb.block', 'b')
            ->andWhere('r.isArchived IS NOT NULL')
            ->andWhere('b.id = :blockId')
            ->setParameter('blockId', $block->getId())
            ->getQuery()
            ->getResult();

        /** @var Revision $revision */
        foreach ($revisions as $revision) {
            /** @var RevisionBlock $revisionBlock */
            foreach ($revision->getBlocks() as $revisionBlock) {
                if ($revisionBlock->getBlock()->getId() === $block->getId()) {
                    $revision->removeBlock($revisionBlock);
                }
            }
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->getFilters()->enable('softdeleteable');
    }
}
