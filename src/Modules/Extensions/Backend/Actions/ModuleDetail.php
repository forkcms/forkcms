<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInformation;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\HttpFoundation\Request;

/**
 * Shows the details of a module.
 */
final class ModuleDetail extends AbstractActionController
{
    public function __construct(ActionServices $services, private readonly ModuleRepository $moduleRepository)
    {
        parent::__construct($services);
    }

    protected function execute(Request $request): void
    {
        $module = ModuleInformation::fromModule(ModuleName::fromString($request->attributes->get('slug')));
        $this->assign('module', $module);
        $this->header->addBreadcrumb(new Breadcrumb($module->getModuleName()));

        if (!array_key_exists($module->getModuleName(), $this->moduleRepository->findAllIndexed())) {
            $module->messages->addMessage(TranslationKey::message('InformationModuleIsNotInstalled'));
            $this->assign(
                'installForm',
                $this->formFactory->create(
                    ActionType::class,
                    [
                        'id' => $module->getModuleName(),
                    ],
                    [
                        'actionSlug' => ModuleInstall::getActionSlug(),
                    ]
                )->createView()
            );
        }
    }
}
