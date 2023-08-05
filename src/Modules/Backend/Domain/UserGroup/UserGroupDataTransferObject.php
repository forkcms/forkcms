<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup;

use Doctrine\Common\Collections\ArrayCollection;
use ForkCMS\Core\Domain\Doctrine\CollectionHelper;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObject;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObjectInterface;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\AjaxAction\ModuleAjaxAction;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\Widget\ModuleWidget;

/** @implements UniqueDataTransferObjectInterface<UserGroup> */
#[UniqueDataTransferObject(['entityClass' => UserGroup::class, 'fields' => ['name']])]
abstract class UserGroupDataTransferObject implements UniqueDataTransferObjectInterface
{
    public ?string $name;

    /** @var ArrayCollection<int|string, User> */
    public ArrayCollection $users;

    public SettingsBag $settings;

    /** @var string[] */
    public array $actions;

    /** @var string[] */
    public array $ajaxActions;

    /** @var string[] */
    public array $widgets;

    public function __construct(protected ?UserGroup $userGroupEntity = null)
    {
        $this->name = $userGroupEntity?->getName();
        $this->users = CollectionHelper::toArrayCollection($userGroupEntity?->getUsers());
        $this->settings = $userGroupEntity?->getSettings() ?? new SettingsBag();
        $roles = $userGroupEntity?->getRoles() ?? [];
        $this->actions = array_map(
            static fn (ModuleAction $moduleAjaxAction): string => $moduleAjaxAction->getFQCN(),
            array_filter(array_map([ModuleAction::class, 'tryFromRole'], $roles))
        );
        $this->ajaxActions = array_map(
            static fn (ModuleAjaxAction $moduleAjaxAction): string => $moduleAjaxAction->getFQCN(),
            array_filter(array_map([ModuleAjaxAction::class, 'tryFromRole'], $roles))
        );
        $this->widgets = array_map(
            static fn (ModuleWidget $moduleAjaxAction): string => $moduleAjaxAction->getFQCN(),
            array_filter(array_map([ModuleWidget::class, 'tryFromRole'], $roles))
        );
    }

    final public function hasEntity(): bool
    {
        return $this->userGroupEntity !== null;
    }

    final public function getEntity(): UserGroup
    {
        return $this->userGroupEntity;
    }
}
