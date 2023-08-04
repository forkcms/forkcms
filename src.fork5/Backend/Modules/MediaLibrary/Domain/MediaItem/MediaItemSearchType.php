<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaItemSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'query',
            TextType::class,
            [
                'label' => 'lbl.Search',
                'required' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'media_item_search';
    }
}
