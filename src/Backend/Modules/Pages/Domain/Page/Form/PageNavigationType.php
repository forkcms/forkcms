<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageNavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'titleOverwrite',
            CheckboxType::class,
            [
                'label' => 'lbl.NavigationTitle',
                'property_path' => 'navigationTitleOverwrite',
                'required' => false,
            ]
        );
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'lbl.NavigationTitle',
                'property_path' => 'navigationTitleOverwrite',
                'label_attr' => ['class' => 'sr-only'],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
                'inherit_data' => true,
            ]
        );
    }
}
