<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SingleMediaGroupType extends MediaGroupType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('minimum_items', 1);
        $resolver->setDefault('maximum_items', 1);
    }
}
