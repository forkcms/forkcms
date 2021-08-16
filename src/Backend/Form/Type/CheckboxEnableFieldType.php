<?php

namespace Backend\Form\Type;

use Backend\Form\DataTransferObject\CheckboxEnabledFieldDataTransferObject;
use Common\Form\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckboxEnableFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'enableField',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $options['enable_field_label']
                ]
            )
            ->add(
                'field',
                TextType::class,
                [
                    'required' => false,
                    'label' => $options['field_label'],
                    'label_attr' => ['class' => 'sr-only']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CheckboxEnabledFieldDataTransferObject::class,
            'enable_field_label' => 'lbl.enableField',
            'field_label' => 'lbl.enabledField',
            'help_text' => ''
        ]);
    }

    public function getBlockPrefix()
    {
        return 'checkbox_enable_field';
    }
}
