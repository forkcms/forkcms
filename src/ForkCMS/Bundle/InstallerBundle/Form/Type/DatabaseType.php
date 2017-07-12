<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Builds the form to set up database information
 */
class DatabaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'databaseHostname',
                'text',
                [
                    'required' => true,
                ]
            )
            ->add(
                'databasePort',
                'text',
                [
                    'required' => true,
                ]
            )
            ->add(
                'databaseName',
                'text',
                [
                    'required' => true,
                ]
            )
            ->add(
                'databaseUsername',
                'text',
                [
                    'required' => true,
                ]
            )
            ->add(
                'databasePassword',
                'password'
            )
        ;

        // make sure the default data is set
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                $databaseHostname = $data->getDatabaseHostname();
                if (empty($databaseHostname) && isset($_SERVER['HTTP_HOST'])) {
                    // guess database & username
                    $host = $_SERVER['HTTP_HOST'];
                    $chunks = explode('.', $host);

                    // seems like windows can't handle localhost...
                    $data->setDatabaseHostname((mb_substr(PHP_OS, 0, 3) == 'WIN') ? '127.0.0.1' : 'localhost');

                    // remove tld
                    array_pop($chunks);

                    // create base
                    $data->setDatabaseName(implode('_', $chunks));
                    $data->setDatabaseUsername(implode('_', $chunks));

                    $event->setData($data);
                }
            }
        );
    }

    public function getName(): string
    {
        return 'install_database';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new Callback(
                    [
                        'methods' => [
                            [$this, 'checkDatabaseConnection'],
                        ],
                    ]
                ),
            ],
            'data_class' => 'ForkCMS\Bundle\InstallerBundle\Entity\InstallationData',
        ]);
    }

    /**
     * Validate if a database connection can be made
     *
     * @param InstallationData $data The form data
     * @param ExecutionContextInterface $context The forms validation context
     *
     * @todo   Replace SpoonDatabase
     */
    public function checkDatabaseConnection(InstallationData $data, ExecutionContextInterface $context): void
    {
        try {
            // create instance
            $database = new \SpoonDatabase(
                'mysql',
                $data->getDatabaseHostname(),
                $data->getDatabaseUsername(),
                $data->getDatabasePassword(),
                $data->getDatabaseName(),
                $data->getDatabasePort()
            );

            // test table
            $table = 'test' . time();

            // attempt to create table
            $database->execute('DROP TABLE IF EXISTS ' . $table);
            $database->execute('CREATE TABLE ' . $table . ' (id int(11) NOT NULL) ENGINE=MyISAM');

            // drop table
            $database->drop($table);
        } catch (\Exception $e) {
            $context->addViolation('Problem with database credentials');
        }
    }
}
