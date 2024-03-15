<?php

namespace ForkCMS\Modules\Backend\Domain\ModuleSettings;

use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Core\Domain\Form\TogglePasswordInputType;
use ForkCMS\Core\Domain\Form\TogglePasswordType;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
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
                SwitchType::class,
                [
                    'required' => false,
                    'label' => TranslationKey::label('EnableTwoFactorAuthentication'),
                ]
            )
            ->add(
                '2fa_key',
                TogglePasswordType::class,
                [
                    'label' => TranslationKey::label('TwoFactorAuthenticationKey'),
                    'required' => false,
                    'constraints' => [
                        new Expression(
                            [
                                'expression' => '!this.getParent().get("2fa_enabled").getData() || (value !== "" && value !== null)',
                                'message' => TranslationKey::error('TwoFactorAuthenticationKeyRequired'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                '2fa_required',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => TranslationKey::label('RequireTwoFactorAuthentication'),
                    'help' => TranslationKey::message('RequireTwoFactorAuthenticationHelp'),
                ]
            )
            ->add(
                'trusted_devices_enabled',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => TranslationKey::label('EnableTrustedDevices'),
                    'help' => TranslationKey::message('EnableTrustedDevicesHelp'),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ChangeModuleSettings::class);
    }
}
