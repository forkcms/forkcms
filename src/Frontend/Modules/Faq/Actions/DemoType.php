<?php

namespace Frontend\Modules\Faq\Actions;

use Common\Form\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'term',
                DatePickerType::class,
                [
                    'label' => 'lbl.Test',
                    'time' => true,
                    'start' => new \DateTime('10/5/2020'),
                    'end' => new \DateTime('10/5/2020'),
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['showCategories' => true]);
    }
}
