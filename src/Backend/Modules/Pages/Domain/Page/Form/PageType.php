<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Common\Form\TitleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TitleType::class,
            [
                'label' => 'lbl.Title',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
            ]
        );
    }
}
