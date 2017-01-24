<?php

namespace Backend\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace(
            $view->vars,
            [
                'allow_add' => $options['allow_add'],
                'allow_delete' => $options['allow_delete'],
                'add_button_text' => $options['add_button_text'],
                'delete_button_text' => $options['delete_button_text'],
                'prototype_name' => $options['prototype_name'],
            ]
        );
        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'allow_add' => false,
                'allow_delete' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'add_button_text' => 'lbl.Add',
                'delete_button_text' => 'lbl.Delete',
                'options' => [],
                'type' => TextType::class,
            ]
        );
        $resolver->setNormalizer(
            'options',
            function (Options $options, $value) {
                $value['block_name'] = 'entry';

                return $value;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SymfonyCollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bootstrap_collection';
    }

    /**
     * Backward compatibility for SF < 3.0
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
