<?php

namespace ForkCMS\Modules\Extensions\Domain\Theme;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\Finder\Finder;
use Throwable;

/**
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[] findAll()
 * @method Theme[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Theme>
 */
final class ThemeRepository extends ServiceEntityRepository
{
    public const THEMES_DIRECTORY = __DIR__ . '/../../../../Themes';

    public function __construct(ManagerRegistry $registry)
    {
        try {
            parent::__construct($registry, Theme::class);
        } catch (Throwable $throwable) {
            if (!empty($_ENV['FORK_DATABASE_HOST']) && $_ENV['APP_ENV'] !== 'test') {
                throw $throwable;
            }
        }
    }

    public function save(Theme $theme): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($theme);
        $entityManager->flush();
    }

    public function remove(Theme $theme): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($theme);
        $entityManager->flush();
    }

    /** @return InstallableTheme[] */
    public function findInstallable(bool $excludeAlreadyInstalled = true): array
    {
        $configFiles = Finder::create()->in(self::THEMES_DIRECTORY)->depth(1)->files()->name('theme.xml');
        $themes = [];
        foreach ($configFiles as $configFile) {
            $theme = InstallableTheme::fromXML($configFile->getRealPath());
            if ($excludeAlreadyInstalled) {
                $alreadyInstalled = $this->createQueryBuilder('t')
                    ->where('t.name = :name')
                    ->setParameter('name', $theme->name)
                    ->getQuery()
                    ->getOneOrNullResult();
                if ($alreadyInstalled !== null) {
                    continue;
                }
                $theme->addMessage(TranslationKey::message('InformationThemeIsNotInstalled'));
            }

            $themes[$theme->name] = $theme;
        }

        return $themes;
    }

    /** @return string[] */
    public static function getThemePaths(): array
    {
        $finder = Finder::create()->in(self::THEMES_DIRECTORY)->depth(1)->files()->name('theme.xml');
        $themes = [];
        foreach ($finder as $configFile) {
            $theme = InstallableTheme::fromXML($configFile->getRealPath());
            $themes[$theme->name] = dirname($configFile->getRealPath());
        }

        return $themes;
    }

    public function activateTheme(Theme $theme): void
    {
        $this->findOneBy(['active' => true])?->deactivate();
        $theme->activate();
        $this->getEntityManager()->flush();
    }
}
