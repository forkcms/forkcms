<?php

namespace Backend\Form\Type;

use Backend\Core\Engine\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction(
            Model::createUrlForAction(
                $options['action'],
                $options['module'],
                null,
                $options['get_parameters']
            )
        );

        $builder->add($options['id_field_name'], HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'module',
            'action',
            'id_field_name',
        ]);

        $resolver->setDefaults([
            'action' => 'Delete',
            'id_field_name' => 'id',
            'get_parameters' => [], // Get parameters to be added to the generated action url
        ]);
    }
}
