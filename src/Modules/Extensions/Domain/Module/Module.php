<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\User\Blameable;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    use Blameable;

    use EntityWithSettingsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'modules__extensions__module__module_name')]
    private ModuleName $name;

    private function __construct(ModuleName $name)
    {
        $this->name = $name;
        $this->settings = new SettingsBag();
    }

    public static function fromString(string $name): self
    {
        return new self(ModuleName::fromString($name));
    }

    public static function fromModuleName(ModuleName $moduleName): self
    {
        return new self($moduleName);
    }

    public function getName(): ModuleName
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name->getName();
    }

    public function getPath(): string
    {
        return realpath(__DIR__ . '/../../../../Modules/' . $this->name);
    }

    public function getAssetsPath(): string
    {
        return $this->getPath() . '/assets';
    }
}
