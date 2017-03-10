<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $titleLabel = 'lbl.Title';

        if ($builder->getData()->getMediaItemEntity()->getType()->isMovie()) {
            $titleLabel = 'lbl.MediaMovieTitle';
            $builder
                ->add(
                    'url',
                    TextType::class,
                    [
                        'label' => 'lbl.MediaMovieId',
                    ]
                );
        }
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => $titleLabel,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_item';
    }
}
