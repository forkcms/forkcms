<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Common\Form\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageNavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'titleOverwrite',
            SwitchType::class,
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
                'property_path' => 'navigationTitle',
                'label_attr' => [
                    'class' => 'visually-hidden',
                ],
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
