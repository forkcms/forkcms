<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Overview of the available templates for the selected theme.
 */
final class ThemeTemplateIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $theme = $this->getTheme($request);
        $this->renderDataGrid(
            ThemeTemplate::class,
            static function (QueryBuilder $queryBuilder) use ($theme): void {
                $queryBuilder->andWhere('ThemeTemplate.theme = :theme')->setParameter('theme', $theme);
            }
        );
        $this->assign('selectedTheme', $theme);
        $this->assign('themes', $this->getRepository(Theme::class)->findAll());
    }

    private function getTheme(Request $request): Theme
    {
        try {
            return $this->getEntityFromRequest($request, Theme::class);
        } catch (NotFoundHttpException) {
            return $this->getRepository(Theme::class)->findOneBy(['active' => true]);
        }
    }
}
