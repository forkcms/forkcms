<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use InvalidArgumentException;
use Symfony\Component\Translation\TranslatableMessage;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
class Translation
{
    use Blameable;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => 'Translation id across locale'])]
    private string $groupId;

    #[ORM\Embedded(class: TranslationDomain::class)]
    private TranslationDomain $domain;

    #[ORM\Embedded(class: TranslationKey::class)]
    private TranslationKey $key;

    #[ORM\Column(type: Types::STRING, length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: Types::TEXT)]
    private string $value;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $source;

    public function __construct(
        TranslationDomain $domain,
        TranslationKey $key,
        Locale $locale,
        string $value,
        string $source = null,
    ) {
        if ($domain->getModuleName() === ModuleName::core()) {
            throw new InvalidArgumentException('Cannot create a translation for the core module');
        }

        $this->id = md5(implode('$', [$domain, $locale->value, $key]));
        $this->groupId = md5(implode('$', [$domain, $key]));
        $this->domain = $domain;
        $this->key = $key;
        $this->locale = $locale;
        $this->value = $value;
        $this->source = $source;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function getDomain(): TranslationDomain
    {
        return $this->domain;
    }

    public function getKey(): TranslationKey
    {
        return $this->key;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /** @param array<int, mixed> $parameters */
    public function getTranslatable(array $parameters = []): TranslatableMessage
    {
        return new TranslatableMessage($this->key->__toString(), $parameters, $this->domain->__toString());
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function change(string $value): void
    {
        $this->value = $value;
    }

    public static function fromDataTransferObject(TranslationDataTransferObject $dataTransferObject): self
    {
        if ($dataTransferObject->hasEntity()) {
            $translation = $dataTransferObject->getEntity();
            $translation->domain = $dataTransferObject->domain;
            $translation->key = $dataTransferObject->key;
            $translation->locale = $dataTransferObject->locale;
            $translation->value = $dataTransferObject->value;
            $translation->source = $dataTransferObject->source;

            return $translation;
        }

        return new self(
            $dataTransferObject->domain,
            $dataTransferObject->key,
            $dataTransferObject->locale,
            $dataTransferObject->value,
            $dataTransferObject->source,
        );
    }
}
