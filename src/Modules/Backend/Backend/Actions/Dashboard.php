<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\Dashboard\Widget;
use ForkCMS\Modules\Backend\Domain\Widget\ModuleWidget;
use ForkCMS\Modules\Backend\Domain\Widget\WidgetControllerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

/**
 * A dashboard displaying widgets of the installed modules.
 */
final class Dashboard extends AbstractActionController
{
    /** @param ServiceLocator<WidgetControllerInterface> $backendDashboardWidgets */
    public function __construct(
        ActionServices $actionServices,
        private readonly ServiceLocator $backendDashboardWidgets,
    ) {
        parent::__construct($actionServices);
    }

    protected function execute(Request $request): void
    {
        $widgets = [];
        foreach ($this->backendDashboardWidgets->getProvidedServices() as $fullyQualifiedClassName) {
            $moduleWidget = ModuleWidget::fromFQCN($fullyQualifiedClassName);
            if (!$this->authorizationChecker->isGranted($moduleWidget->asRole())) {
                continue;
            }

            $widgets[] = new Widget(
                $moduleWidget->getModule()->asLabel(),
                $moduleWidget->getWidget()->asLabel(),
                $this->backendDashboardWidgets->get($fullyQualifiedClassName)($request)
            );
        }
        $this->assign('widgets', $widgets);
    }
}
