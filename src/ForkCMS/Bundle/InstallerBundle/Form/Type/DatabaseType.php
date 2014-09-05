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
                'hostname',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'port',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'database',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'username',
                'text',
                array(
                    'required' => true,
                )
            )
            ->add(
                'password',
                'password'
            )
        ;

        // make sure the default data is set
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                if (empty($data)) {
                    $data = array();

                    // guess db & username
                    $host = $_SERVER['HTTP_HOST'];
                    $chunks = explode('.', $host);

                    // seems like windows can't handle localhost...
                    $data['hostname'] = (substr(PHP_OS, 0, 3) == 'WIN') ? '127.0.0.1' : 'localhost';

                    // remove tld
                    array_pop($chunks);

                    // create base
                    $data['database'] = $data['username'] = implode('_', $chunks);
                    $data['port'] = 3306;

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
                $data['hostname'],
                $data['username'],
                $data['password'],
                $data['database'],
                $data['port']
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
