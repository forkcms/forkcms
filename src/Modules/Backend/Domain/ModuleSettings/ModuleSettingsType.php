<?php

namespace ForkCMS\Modules\Backend\Domain\ModuleSettings;

use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Expression;

final class ModuleSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                '2fa_enabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => ['class' => 'checkbox-switch'],
                ]
            )
            ->add(
                '2fa_key',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                '2fa_required',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => ['class' => 'checkbox-switch'],
                ]
            )
            ->add(
                'trusted_devices_enabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => ['class' => 'checkbox-switch'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ChangeModuleSettings::class);
    }
}
