<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Assert\Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[ORM\Embeddable]
class TranslationKey implements TranslatableInterface
{
    #[ORM\Column(type: Types::STRING, length: 10, enumType: Type::class)]
    private Type $type;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /** @var array<string, string|int|float|Stringable> */
    private array $parameters = [];

    private function __construct(Type $type, string $name)
    {
        $this->type = $type;
        Assert::that($name)->same(Container::camelize($name), 'The name should be in CamelCase');
        $this->name = Container::camelize($name);
    }

    public static function forType(Type $type, string $name): self
    {
        return new self($type, $name);
    }

    public static function label(string $name): self
    {
        return new self(Type::LABEL, $name);
    }

    public static function message(string $name): self
    {
        return new self(Type::MESSAGE, $name);
    }

    public static function error(string $name): self
    {
        return new self(Type::ERROR, $name);
    }

    public static function slug(string $name): self
    {
        return new self(Type::SLUG, $name);
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->type->getAbbreviation() . '.' . $this->name;
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans((string) $this, $this->parameters, null, $locale);
    }

    /** @param array<string, string|int|float|Stringable> $parameters */
    public function withParameters(array $parameters): self
    {
        $translationKey = clone $this;
        $translationKey->parameters = $parameters;

        return $translationKey;
    }

    public function equals(?self $other): bool
    {
        if ($other === null) {
            return false;
        }

        return $this->type === $other->type && $this->name === $other->name;
    }
}
