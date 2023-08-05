<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\Theme\InstallableTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Shows the details of a theme.
 */
final class ThemeDetail extends AbstractActionController
{
    public function __construct(ActionServices $services, private readonly ThemeRepository $themeRepository)
    {
        parent::__construct($services);
    }

    protected function execute(Request $request): void
    {
        $name = $request->attributes->get('slug');
        try {
            $theme = $this->getEntityFromRequest($request, Theme::class);
        } catch (NotFoundHttpException) {
            $theme = $this->themeRepository->findInstallable()[$name]
                ?? InstallableTheme::fromMessage(TranslationKey::error('InformationFileIsMissing'));
        }

        if ($theme instanceof Theme) {
            $theme = InstallableTheme::fromTheme($theme);
        }

        $this->assign('theme', $theme);
        if ($theme->name !== null) {
            $this->assign(
                'themeInstallForm',
                $this->formFactory->create(
                    ActionType::class,
                    [
                        'name' => $theme->name,
                    ],
                    [
                        'id_field_name' => 'name',
                        'actionSlug' => ThemeInstall::getActionSlug(),
                    ]
                )->createView()
            );
            $this->header->addBreadcrumb(new Breadcrumb($theme->name));
        }
    }
}
