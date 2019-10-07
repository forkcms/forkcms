<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SingleMediaGroupType extends MediaGroupType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options['minimum_items'] = 1;
        $options['maximum_items'] = 1;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->remove(
            [
                'minimum_items',
                'maximum_items',
            ]
        );
    }
}
