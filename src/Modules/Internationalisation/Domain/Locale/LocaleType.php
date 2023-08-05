<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocaleType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'class' => Locale::class,
                'label' => 'lbl.Locale',
                'choice_label' => fn (Locale $locale): string => ucfirst($locale->trans($this->translator)),
                'choice_translation_domain' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'fork_locale';
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
