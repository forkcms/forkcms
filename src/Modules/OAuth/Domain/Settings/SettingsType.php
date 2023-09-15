<?php

namespace ForkCMS\Modules\OAuth\Domain\Settings;

use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Modules\OAuth\Domain\Settings\Command\UpdateModuleSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'clientId',
                TextType::class,
                [
                    'label' => 'lbl.ClientId',
                    'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                ]
            )
            ->add(
                'clientSecret',
                TextType::class,
                [
                    'label' => 'lbl.ClientSecret',
                    'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                ]
            )
            ->add(
                'tenant',
                TextType::class,
                [
                    'label' => 'lbl.Tenant',
                    'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                ]
            )
            ->add(
                'enabled',
                SwitchType::class,
                [
                    'label' => 'lbl.Enabled',
                    'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UpdateModuleSettings::class,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'oauth_settings';
    }
}
