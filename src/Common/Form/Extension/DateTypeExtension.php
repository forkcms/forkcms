<?php

namespace Common\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class DateTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [
            DateType::class,
        ];
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];

        if (!array_key_exists('class', $attr)) {
            $attr['class'] = '';
        }

        $classes = explode(' ', $attr['class']);
        if (!in_array('inputDatefield', $classes, true)) {
            $classes[] = 'inputDatefield';
        }

        $attr['class'] = implode(' ', $classes);

        if (!array_key_exists('data-mask', $attr)) {
            $attr['data-mask'] = 'dd/mm/yy';
        }

        if (!array_key_exists('data-firstday', $attr)) {
            $attr['data-firstday'] = '1';
        }

        if (!array_key_exists('data-year', $attr)) {
            $attr['data-year'] = 'Y';
        }

        if (!array_key_exists('data-month', $attr)) {
            $attr['data-month'] = date('n') - 1;
        }

        if (!array_key_exists('data-day', $attr)) {
            $attr['data-day'] = date('j');
        }

        if (!array_key_exists('autocomplete', $attr)) {
            $attr['autocomplete'] = 'off';
        }

        $view->vars['attr'] = $attr;
    }
}
