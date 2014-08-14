<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Builds the form to select languages to install
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class LanguagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('same_interface_language', 'checkbox')
        ;
    }

    public function getName()
    {
        return 'install_languages';
    }
}
