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
 * Builds the form to set up login information
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                'email'
            )
            ->add('password', 'repeated', array(
                    'type'            => 'password',
                    'invalid_message' => 'The passwords do not match.',
                    'required'        => true,
                    'first_options'   => array('label' => 'Password'),
                    'second_options'  => array('label' => 'Confirm'),
                )
            )
        ;

        // make sure the default data is set
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                if (empty($data)) {
                    $data = array();
                    $data['email'] = 'info@' . $_SERVER['HTTP_HOST'];
                    $event->setData($data);
                }
            }
        );
    }

    public function getName()
    {
        return 'install_login';
    }
}
