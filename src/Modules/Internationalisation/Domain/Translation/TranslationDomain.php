<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Symfony\Component\DependencyInjection\Container;

#[ORM\Embeddable]
class TranslationDomain
{
    #[ORM\Column(type: Types::STRING, length: 10, enumType: Application::class)]
    private Application $application;

    #[ORM\Column(type: 'modules__extensions__module__module_name', nullable: true)]
    private ?ModuleName $moduleName;

    public function __construct(Application $application, ?ModuleName $moduleName = null)
    {
        $this->application = $application;
        $this->moduleName = $moduleName;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getModuleName(): ?ModuleName
    {
        return $this->moduleName;
    }

    public function getDomain(): string
    {
        return Container::underscore($this->application->value . $this->moduleName);
    }

    public function __toString(): string
    {
        return $this->getDomain();
    }

    public static function fromDomain(string $domain): self
    {
        $domainParts = explode('_', $domain, 2);

        return new self(
            Application::from(strtolower($domainParts[0])),
            array_key_exists(1, $domainParts) ? ModuleName::fromString(Container::camelize($domainParts[1])) : null
        );
    }

    public function getFallback(): ?self
    {
        if ($this->moduleName === null) {
            return null;
        }

        return new self($this->application);
    }
}
