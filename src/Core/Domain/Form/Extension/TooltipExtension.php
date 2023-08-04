<?php

namespace ForkCMS\Core\Domain\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TooltipExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('label_tooltip', null);
        $optionsResolver->setDefault('label_tooltip_translation_domain', null);
        $optionsResolver->setDefault('label_tooltip_translation_arguments', []);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['label_tooltip'] = $options['label_tooltip'];
        $view->vars['label_tooltip_translation_domain'] = $options['label_tooltip_translation_domain'];
        $view->vars['label_tooltip_translation_arguments'] = $options['label_tooltip_translation_arguments'];
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            TextType::class,
        ];
    }
}
