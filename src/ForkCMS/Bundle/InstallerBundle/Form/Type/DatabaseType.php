<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
                'text',
                array(
                    'attr' => array(
                        'value' => 3306,
                    ),
                )
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
    }

    public function getName()
    {
        return 'install_database';
    }
}
