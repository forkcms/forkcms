<?php

namespace Backend\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'attr' => [
                    'class' => 'js-tags-input',
                    'aria-describedby' => 'tags-info',
                ],
                'label' => 'lbl.Tags',
            ]
        );
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'tags';
    }
}
