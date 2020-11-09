<?php

namespace Common\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends DateType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Set all the fixed attributes required to make Flatpickr work
        $view->vars['attr']['data-role'] = 'fork-datepicker';
        $view->vars['attr']['data-date-format'] = 'd/m/Y';

        // Parse the optional parameters
        if (isset($options['start'])) {
            $view->vars['attr']['data-min-date'] = $options['start']->format('d/m/Y');
        }

        if (isset($options['end'])) {
            $view->vars['attr']['data-max-date'] = $options['end']->format('d/m/Y');
        }

        if (isset($options['time'])) {
            $view->vars['attr']['data-enable-time'] = $options['time'];
        }

        parent::buildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('widget', 'single_text');
        $resolver->setDefault('html5', false);
        $resolver->setDefault('format', 'dd/MM/yyyy');
        $resolver->setDefault('start', null);
        $resolver->setDefault('end', null);
        $resolver->setDefault('time', false);
    }
}
