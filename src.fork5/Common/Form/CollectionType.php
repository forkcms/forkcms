<?php

namespace Common\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace(
            $view->vars,
            [
                'allow_add' => $options['allow_add'],
                'allow_delete' => $options['allow_delete'],
                'allow_sequence' => $options['allow_sequence'],
                'sequence_group' => $options['sequence_group'],
                'add_button_text' => $options['add_button_text'],
                'delete_button_text' => $options['delete_button_text'],
                'prototype_name' => $options['prototype_name'],
            ]
        );
        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'allow_add' => false,
                'allow_delete' => false,
                'allow_sequence' => false,
                'sequence_group' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'add_button_text' => 'lbl.Add',
                'delete_button_text' => 'lbl.Delete',
                'entry_options' => [],
                'entry_type' => TextType::class,
            ]
        );
        $resolver->setNormalizer(
            'entry_options',
            function (Options $options, $value) {
                $value['block_name'] = 'entry';

                return $value;
            }
        );
    }

    public function getParent(): string
    {
        return SymfonyCollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'bootstrap_collection';
    }
}
