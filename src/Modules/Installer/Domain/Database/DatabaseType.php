<?php

namespace ForkCMS\Modules\Installer\Domain\Database;

use ForkCMS\Modules\Installer\Domain\Authentication\InstallerPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Builds the form to set up database information.
 */
class DatabaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('databaseHostname', TextType::class)
            ->add('databasePort', TextType::class)
            ->add('databaseName', TextType::class)
            ->add('databaseUsername', TextType::class)
            ->add('databasePassword', InstallerPasswordType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'install_database';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'constraints' => [
                    new Callback(
                        [
                            'callback' => static function (
                                DatabaseStepConfiguration $data,
                                ExecutionContextInterface $context
                            ): void {
                                if (!$data->canConnectToDatabase()) {
                                    $context->addViolation('Problem with database credentials');
                                }
                            },
                        ]
                    ),
                ],
                'data_class' => DatabaseStepConfiguration::class,
            ]
        );
    }
}
