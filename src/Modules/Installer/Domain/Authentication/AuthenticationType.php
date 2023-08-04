<?php

namespace ForkCMS\Modules\Installer\Domain\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

/**
 * Builds the form to set up login information.
 */
final class AuthenticationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => InstallerPasswordType::class,
                    'invalid_message' => 'The passwords do not match.',
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm'],
                    'constraints' => [
                        new NotCompromisedPassword(['skipOnError' => true]),
                    ],
                ]
            )->add(
                'differentDebugEmail',
                CheckboxType::class,
                [
                    'label' => 'Use a specific debug email address',
                    'required' => false,
                    'attr' => [
                        'data-fork-cms-role' => 'different-debug-email',
                    ],
                ]
            )->add(
                'debugEmail',
                EmailType::class,
                [
                    'required' => false,
                    'attr' => [
                        'data-fork-cms-role' => 'debug-email',
                    ],
                ]
            )->add(
                'saveConfiguration',
                CheckboxType::class,
                [
                    'label' => 'Save the installation configuration to a yaml file for future use',
                    'required' => false,
                    'attr' => [
                        'data-fork-cms-role' => 'save-configuration',
                    ],
                ]
            )->add(
                'saveConfigurationWithCredentials',
                CheckboxType::class,
                [
                    'label' => 'Include database and admin credentials (at own risk)',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => AuthenticationStepConfiguration::class,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'install_authentication';
    }
}
