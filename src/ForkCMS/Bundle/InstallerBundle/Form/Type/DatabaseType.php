<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;

/**
 * Builds the form to set up database information
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class DatabaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'dbHostname',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'dbPort',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'dbDatabase',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'dbUsername',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'dbPassword',
                'password'
            )
        ;

        // make sure the default data is set
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                $dbHostname = $data->getDbHostname();
                if (empty($dbHostname)) {
                    // guess db & username
                    $host = $_SERVER['HTTP_HOST'];
                    $chunks = explode('.', $host);

                    // seems like windows can't handle localhost...
                    $data->setDbHostname((substr(PHP_OS, 0, 3) == 'WIN') ? '127.0.0.1' : 'localhost');

                    // remove tld
                    array_pop($chunks);

                    // create base
                    $data->setDbDatabase(implode('_', $chunks));
                    $data->setDbUsername(implode('_', $chunks));

                    $event->setData($data);
                }
            }
        );
    }

    public function getName()
    {
        return 'install_database';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'constraints' => array(
                new Callback(
                    array(
                        'methods' => array(
                            array($this, 'checkDatabaseConnection')
                        ),
                    )
                )
            ),
            'data_class' => 'ForkCMS\Bundle\InstallerBundle\Entity\InstallationData',
        ));
    }

    /**
     * Validate if a database connection can be made
     *
     * @param array                     $data    The form data
     * @param ExecutionContextInterface $context The forms validation context
     *
     * @todo   Replace SpoonDatabase
     */
    public function checkDatabaseConnection($data, ExecutionContextInterface $context)
    {
        try {
            // create instance
            $db = new \SpoonDatabase(
                'mysql',
                $data->getDbHostname(),
                $data->getDbUsername(),
                $data->getDbPassword(),
                $data->getDbDatabase(),
                $data->getDbPort()
            );

            // test table
            $table = 'test' . time();

            // attempt to create table
            $db->execute('DROP TABLE IF EXISTS ' . $table);
            $db->execute('CREATE TABLE ' . $table . ' (id int(11) NOT NULL) ENGINE=MyISAM');

            // drop table
            $db->drop($table);
        } catch (\Exception $e) {
            $context->addViolation('Problem with database credentials');
        }
    }
}
