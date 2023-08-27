<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Assert\Assert;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Stringable;

#[ORM\Embeddable]
final class ModuleBlock implements Stringable
{
    #[ORM\Column(type: 'modules__extensions__module__module_name')]
    private ModuleName $module;

    #[ORM\Column(type: 'modules__frontend__block__block_name')]
    private BlockName $name;

    public function __construct(ModuleName $module, BlockName $name)
    {
        $this->module = $module;
        $this->name = $name;
        Assert::that($this->getFQCN())->classExists('Action class not found');
    }

    public static function fromFQCN(string $fullyQualifiedClassName): self
    {
        static $cache = [];
        if (array_key_exists($fullyQualifiedClassName, $cache)) {
            return $cache[$fullyQualifiedClassName];
        }

        $matches = [];
        if (
            !preg_match(
                '/^ForkCMS\\\Modules\\\([A-Z]\w*)\\\Frontend\\\(Actions|Widgets)\\\([A-Z]\w*$)/',
                $fullyQualifiedClassName,
                $matches
            )
        ) {
            throw new InvalidArgumentException('Can ony be created from a frontend action or widget');
        }


        $cache[$fullyQualifiedClassName] = new self(
            ModuleName::fromString($matches[1]),
            Type::fromDirectoryName($matches[2])->getBlockName($matches[3])
        );

        return $cache[$fullyQualifiedClassName];
    }

    public function getFQCN(): string
    {
        return sprintf(
            'ForkCMS\\Modules\\%1$s\\Frontend\\%2$s\\%3$s',
            $this->module,
            $this->name->getType()->getDirectoryName(),
            $this->name->getName()
        );
    }

    public function __toString(): string
    {
        return $this->getFQCN();
    }

    public function getModule(): ModuleName
    {
        return $this->module;
    }

    public function getName(): BlockName
    {
        return $this->name;
    }
}
