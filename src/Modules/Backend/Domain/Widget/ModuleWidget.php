<?php

namespace ForkCMS\Modules\Backend\Domain\Widget;

use Assert\Assert;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Stringable;
use Symfony\Component\DependencyInjection\Container;

#[ORM\Embeddable]
final class ModuleWidget implements Stringable
{
    public const ROLE_PREFIX = 'ROLE_MODULE_WIDGET__';

    #[ORM\Column(type: 'modules__extensions__module__module_name')]
    private ModuleName $module;

    #[ORM\Column(type: 'modules__backend__widget__widget_name')]
    private WidgetName $widget;

    public function __construct(ModuleName $module, WidgetName $widget)
    {
        $this->module = $module;
        $this->widget = $widget;
        Assert::that($this->getFQCN())->classExists('Widget class not found');
    }

    public static function fromFQCN(string $fullyQualifiedClassName): self
    {
        $matches = [];
        if (
            !preg_match(
                '/^ForkCMS\\\Modules\\\([A-Z]\w*)\\\Backend\\\Widgets\\\([A-Z]\w*$)/',
                $fullyQualifiedClassName,
                $matches
            )
        ) {
            throw new InvalidArgumentException('Can ony be created from a backend widget class name');
        }

        return new self(ModuleName::fromString($matches[1]), WidgetName::fromString($matches[2]));
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

        [$moduleName, $widgetName] = explode('__', strtolower(substr($role, strlen(self::ROLE_PREFIX))));

        return new self(
            ModuleName::fromString(Container::camelize($moduleName)),
            WidgetName::fromString(Container::camelize($widgetName))
        );
    }

    public function getFQCN(): string
    {
        return 'ForkCMS\\Modules\\' . $this->module . '\\Backend\\Widgets\\' . $this->widget;
    }

    public function __toString(): string
    {
        return $this->getFQCN();
    }

    public function getModule(): ModuleName
    {
        return $this->module;
    }

    public function getWidget(): WidgetName
    {
        return $this->widget;
    }

    public function asRole(): string
    {
        $identifier = Container::underscore($this->module->getName()) . '__' .
            Container::underscore($this->widget->getName());

        return self::ROLE_PREFIX . strtoupper($identifier);
    }
}
