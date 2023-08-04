<?php

namespace ForkCMS\Modules\Backend\Domain\AjaxAction;

use Assert\Assert;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Stringable;
use Symfony\Component\DependencyInjection\Container;

#[ORM\Embeddable]
final class ModuleAjaxAction implements Stringable
{
    public const ROLE_PREFIX = 'ROLE_MODULE_AJAX_ACTION__';

    #[ORM\Column(type: 'modules__extensions__module__module_name')]
    private ModuleName $module;

    #[ORM\Column(type: 'modules__backend__ajax_action__ajax_action_name')]
    private AjaxActionName $action;

    public function __construct(ModuleName $module, AjaxActionName $action)
    {
        $this->module = $module;
        $this->action = $action;
        Assert::that($this->getFQCN())->classExists('Ajax action class not found');
    }

    public static function fromFQCN(string $fullyQualifiedClassName): self
    {
        $matches = [];
        if (
            !preg_match(
                '/^ForkCMS\\\Modules\\\([A-Z]\w*)\\\Backend\\\Ajax\\\([A-Z]\w*$)/',
                $fullyQualifiedClassName,
                $matches
            )
        ) {
            throw new InvalidArgumentException('Can ony be created from a backend ajax action class name');
        }

        return new self(ModuleName::fromString($matches[1]), AjaxActionName::fromString($matches[2]));
    }

    public static function fromRole(string $role): self
    {
        return self::tryFromRole($role)
            ?? throw new InvalidArgumentException('Role should start with: ' . self::ROLE_PREFIX);
    }

    public static function tryFromRole(string $role): ?self
    {
        if (!str_starts_with($role, self::ROLE_PREFIX)) {
            return null;
        }

        [$moduleName, $ajaxActionName] = explode('__', strtolower(substr($role, strlen(self::ROLE_PREFIX))));

        return new self(
            ModuleName::fromString(Container::camelize($moduleName)),
            AjaxActionName::fromString(Container::camelize($ajaxActionName))
        );
    }

    public function getFQCN(): string
    {
        return 'ForkCMS\\Modules\\' . $this->module . '\\Backend\\Ajax\\' . $this->action;
    }

    public function __toString(): string
    {
        return $this->getFQCN();
    }

    public function getModule(): ModuleName
    {
        return $this->module;
    }

    public function getAction(): AjaxActionName
    {
        return $this->action;
    }

    public function asRole(): string
    {
        $identifier = Container::underscore($this->module->getName()) . '__' .
                      Container::underscore($this->action->getName());

        return self::ROLE_PREFIX . strtoupper($identifier);
    }
}
