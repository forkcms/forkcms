<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/** @implements DataTransformerInterface<TranslationKey,array> */
final class TranslationKeyType extends AbstractType implements DataTransformerInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'type',
            EnumType::class,
            [
                'class' => Type::class,
                'label' => 'lbl.Type',
                'choice_label' => fn (Type $type): string => ucfirst($type->trans($this->translator)),
                'choice_translation_domain' => false,
            ]
        )->add(
            'name',
            TextType::class,
            [
                'label' => 'lbl.ReferenceCode',
                'help' => 'msg.HelpReferenceCode',
                'constraints' => [
                    new NotBlank(message: 'err.FieldIsRequired'),
                    new Regex(pattern: '/^([a-z0-9])+$/i', message: 'err.AlphaNumericCharactersOnly'),
                    new Regex(pattern: '/^[A-Z]/', message: 'err.FirstLetterMustBeACapitalLetter'),
                ],
            ]
        )->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('label', false);
        $resolver->setDefault('error_bubbling', false);
    }

    /**
     * @param TranslationKey|null $value
     *
     * @return array{type?:Type, name?:string}
     */
    public function transform(mixed $value): array
    {
        if ($value instanceof TranslationKey) {
            return [
                'type' => $value->getType(),
                'name' => $value->getName(),
            ];
        }

        return [];
    }

    /** @param array{type:Type, name:string} $value */
    public function reverseTransform(mixed $value): ?TranslationKey
    {
        try {
            return TranslationKey::forType($value['type'], $value['name']);
        } catch (Throwable) {
            return null;
        }
    }
}
