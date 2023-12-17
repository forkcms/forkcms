<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command\DeleteThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Delete a theme template.
 */
final class ThemeTemplateDelete extends AbstractDeleteActionController
{
    public function getFormResponse(Request $request): Response
    {
        $themeTemplate = $this->getEntityFromRequestOrNull($request, ThemeTemplate::class, 'action.id');

        return $this->handleDeleteForm(
            $request,
            DeleteThemeTemplate::class,
            ThemeTemplateIndex::getActionSlug()->withDefaultParameters(
                ['slug' => $themeTemplate?->getTheme()?->getName()]
            ),
            FlashMessage::success('ThemeTemplateDeleted', ['%template%' => $themeTemplate?->getName()]),
        );
    }
}
