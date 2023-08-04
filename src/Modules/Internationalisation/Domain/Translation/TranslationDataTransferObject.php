<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObject;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObjectInterface;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

/** @implements UniqueDataTransferObjectInterface<Translation> */
#[UniqueDataTransferObject([
    'entityClass' => Translation::class,
    'fields' => ['domain', 'key', 'locale'],
    'repositoryMethod' => 'uniqueDataTransferObjectMethod',
    'message' => 'err.TranslationAlreadyExists',
])]
abstract class TranslationDataTransferObject implements UniqueDataTransferObjectInterface
{
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?TranslationDomain $domain;

    #[Assert\Valid]
    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?TranslationKey $key;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?Locale $locale;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?string $value;

    public ?string $source;

    public function __construct(protected ?Translation $translationEntity = null)
    {
        $this->domain = $translationEntity?->getDomain();
        $this->key = $translationEntity?->getKey();
        $this->locale = $translationEntity?->getLocale();
        $this->value = $translationEntity?->getValue();
        $this->source = $translationEntity?->getSource();
    }

    public function hasEntity(): bool
    {
        return $this->translationEntity !== null;
    }

    public function getEntity(): Translation
    {
        return $this->translationEntity;
    }
}
