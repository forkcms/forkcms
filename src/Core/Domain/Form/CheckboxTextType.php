<?php

namespace ForkCMS\Core\Domain\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class CheckboxTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requiredGroupName = $builder->getName() . '_required';

        $builder->add(
            $builder->getName() . $options['checkbox_name_suffix'],
            CheckboxType::class,
            [
                'required' => false,
                'label' => 'lbl.Enable',
                'label_attr' => ['class' => 'visually-hidden'],
            ]
        )->add(
            $builder->getName() . $options['text_name_suffix'],
            TextType::class,
            [
                'required' => false,
                'label' => 'lbl.Value',
                'label_attr' => ['class' => 'visually-hidden'],
                'constraints' => [new NotBlank(message: 'err.FieldIsRequired', groups: [$requiredGroupName])],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('checkbox_name_suffix', '_enabled');
        $resolver->setDefault('text_name_suffix', '_value');
        $resolver->setDefault('inherit_data', true);
        $resolver->setDefault('row_attr', ['class' => 'form-group checkboxTextFieldCombo']);
        $resolver->setDefault(
            'validation_groups',
            static function (FormInterface $form) {
                $checkboxName = $form->getName() . $form->getConfig()->getOption('checkbox_name_suffix');
                $isRequired = $form->getData()->module->getSetting($checkboxName, false);

                if ($isRequired) {
                    return ['default', $form->getName() . '_required'];
                }

                return ['default'];
            }
        );
    }
}
