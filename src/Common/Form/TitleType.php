<?php

namespace Common\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleType extends TextType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'label' => 'lbl.Title',
                'required' => true,
                'attr' => [
                    'class' => 'form-control title',
                ]
            ]
        );
    }
}
