<?php

namespace Common\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends DateType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('widget', 'single_text');
        $resolver->setDefault('html5', false);
        $resolver->setDefault('date_format', 'dd/MM/yyyy');
        $resolver->setDefault(
            'attr',
            [
                'data-role' => 'fork-datepicker',
            ]
        );
    }
}
