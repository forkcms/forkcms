<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup;

use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Modules\Backend\Backend\Actions\AuthenticationLogin;
use ForkCMS\Modules\Backend\Backend\Actions\AuthenticationResetPassword;
use ForkCMS\Modules\Backend\Backend\Actions\Forbidden as ActionForbidden;
use ForkCMS\Modules\Backend\Backend\Actions\NotFound as ActionNotFound;
use ForkCMS\Modules\Backend\Backend\Ajax\Forbidden as AjaxForbidden;
use ForkCMS\Modules\Backend\Backend\Ajax\NotFound as AjaxNotFound;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\AjaxAction\ModuleAjaxAction;
use ForkCMS\Modules\Backend\Domain\User\UserDataGridChoiceType;
use ForkCMS\Modules\Backend\Domain\UserGroup\Permission\Permission;
use ForkCMS\Modules\Backend\Domain\UserGroup\Permission\PermissionType;
use ForkCMS\Modules\Backend\Domain\Widget\ModuleWidget;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class UserGroupType extends AbstractType
{
    /**
     * @param ServiceLocator $backendActions
     * @param ServiceLocator $backendAjaxActions
     * @param ServiceLocator $backendDashboardWidgets
     */
    public function __construct(
        private ServiceLocator $backendActions,
        private ServiceLocator $backendAjaxActions,
        private ServiceLocator $backendDashboardWidgets,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $actions = $this->getAvailableActions();
        $ajaxActions = $this->getAvailableAjaxActions();
        $widgets = $this->getAvailableWidgets();

        $tabs = [
            'lbl.Name' => static function (FormBuilderInterface $builder): void {
                $builder->add(
                    'name',
                    TextType::class,
                    [
                        'label' => 'lbl.Name',
                        'required' => true,
                    ]
                );
            },
        ];
        if (count($widgets) > 0) {
            $tabs['lbl.Dashboard'] = static function (FormBuilderInterface $builder) use ($widgets): void {
                $builder->add(
                    'widgets',
                    PermissionType::class,
                    [
                        'choices' => $widgets,
                        'name_label' => 'lbl.Widget',
                        'transform_callback' => static function (array $widgetFQCNs) use ($widgets): array {
                            $permissions = [];
                            foreach ($widgetFQCNs as $widgetFQCN) {
                                $permissions[] = $widgets[$widgetFQCN];
                            }

                            return $permissions;
                        },
                    ]
                );
            };
        }
        if (count($actions) > 0) {
            $tabs['lbl.Actions'] = static function (FormBuilderInterface $builder) use ($actions): void {
                $builder->add(
                    'actions',
                    PermissionType::class,
                    [
                        'choices' => $actions,
                        'name_label' => 'lbl.Action',
                        'transform_callback' => static function (array $actionFQCNs) use ($actions): array {
                            $permissions = [];
                            foreach ($actionFQCNs as $actionFQCN) {
                                $permissions[] = $actions[$actionFQCN];
                            }

                            return $permissions;
                        },
                    ]
                );
            };
        }
        if (count($ajaxActions) > 0) {
            $tabs['lbl.AjaxActions'] = static function (FormBuilderInterface $builder) use ($ajaxActions): void {
                $builder->add(
                    'ajaxActions',
                    PermissionType::class,
                    [
                        'choices' => $ajaxActions,
                        'name_label' => 'lbl.Action',
                        'transform_callback' => static function (array $ajaxActionFQCNs) use (
                            $ajaxActions
                        ): array {
                            $permissions = [];
                            foreach ($ajaxActionFQCNs as $ajaxActionFQCN) {
                                $permissions[] = $ajaxActions[$ajaxActionFQCN];
                            }

                            return $permissions;
                        },
                    ]
                );
            };
        }
        $tabs['lbl.Users'] = static function (FormBuilderInterface $builder): void {
            $builder->add(
                'users',
                UserDataGridChoiceType::class,
                [
                    'required' => false,
                ]
            );
        };
        $builder->add(
            'userGroup',
            TabsType::class,
            [
                'label' => 'lbl.Name',
                'tabs' => $tabs,
            ]
        );
    }

    /** @return array<string, Permission> */
    private function getAvailableActions(): array
    {
        $actions = array_map(
            static function (string $fullyQualifiedClassName): Permission {
                $moduleAction = ModuleAction::fromFQCN($fullyQualifiedClassName);

                return new Permission(
                    $fullyQualifiedClassName,
                    $moduleAction->getModule()->getName(),
                    $moduleAction->getAction()->getName(),
                    self::getClassDescription($fullyQualifiedClassName),
                );
            },
            array_filter(
                $this->backendActions->getProvidedServices(),
                fn (string $fullyQualifiedClassName) => $this->authorizationChecker->isGranted(
                    ModuleAction::fromFQCN($fullyQualifiedClassName)->asRole()
                )
            )
        );

        unset(
            $actions[ActionForbidden::class],
            $actions[ActionNotFound::class],
            $actions[AuthenticationLogin::class],
            $actions[AuthenticationResetPassword::class],
        );

        return $actions;
    }

    /** @return array<string, Permission> */
    private function getAvailableWidgets(): array
    {
        return array_map(
            static function (string $fullyQualifiedClassName): Permission {
                $moduleAction = ModuleWidget::fromFQCN($fullyQualifiedClassName);

                return new Permission(
                    $fullyQualifiedClassName,
                    $moduleAction->getModule()->getName(),
                    $moduleAction->getWidget()->getName(),
                    self::getClassDescription($fullyQualifiedClassName),
                );
            },
            array_filter(
                $this->backendDashboardWidgets->getProvidedServices(),
                fn (string $fullyQualifiedClassName) => $this->authorizationChecker->isGranted(
                    ModuleWidget::fromFQCN($fullyQualifiedClassName)->asRole()
                )
            )
        );
    }

    /** @return array<string, Permission> */
    private function getAvailableAjaxActions(): array
    {
        $ajaxActions = array_map(
            static function (string $fullyQualifiedClassName): Permission {
                $moduleAjaxAction = ModuleAjaxAction::fromFQCN($fullyQualifiedClassName);

                return new Permission(
                    $fullyQualifiedClassName,
                    $moduleAjaxAction->getModule()->getName(),
                    $moduleAjaxAction->getAction()->getName(),
                    self::getClassDescription($fullyQualifiedClassName),
                );
            },
            array_filter(
                $this->backendAjaxActions->getProvidedServices(),
                fn (string $fullyQualifiedClassName) => $this->authorizationChecker->isGranted(
                    ModuleAjaxAction::fromFQCN($fullyQualifiedClassName)->asRole()
                )
            )
        );

        unset(
            $ajaxActions[AjaxForbidden::class],
            $ajaxActions[AjaxNotFound::class],
        );

        return $ajaxActions;
    }

    private static function getClassDescription(string $fullyQualifiedClassName): string
    {
        $reflection = new ReflectionClass($fullyQualifiedClassName);
        $phpDoc = trim($reflection->getDocComment());
        if ($phpDoc === '') {
            return '';
        }

        $offset = mb_strpos($reflection->getDocComment(), '*', 7);
        $description = mb_substr($reflection->getDocComment(), 0, $offset);
        $description = str_replace('*', '', $description);

        return trim(str_replace('/', '', $description));
    }
}
