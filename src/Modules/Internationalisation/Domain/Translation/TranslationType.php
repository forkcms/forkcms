<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('domain', TranslationDomainType::class)
            ->add('key', TranslationKeyType::class)
            ->add('locale', InstalledLocaleType::class)
            ->add('value', TextareaType::class, ['label' => 'lbl.Translation']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', TranslationDataTransferObject::class);
    }
}
