<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                'text'
            )
            ->add(
                'port',
                'text'
            )
            ->add(
                'database',
                'text'
            )
            ->add(
                'username',
                'text'
            )
            ->add(
                'password',
                'text'
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
}
