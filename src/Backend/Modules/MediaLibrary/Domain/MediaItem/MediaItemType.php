<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class MediaItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $label = 'lbl.Title';

        if ($builder->getData()->getMediaItemEntity()->getType()->isMovie()) {
            $label = 'lbl.MediaMovieTitle';
            $builder->add(
                'url',
                TextType::class,
                [
                    'label' => 'lbl.MediaMovieId',
                    'constraints' => [
                        new Regex(['pattern' => '/^[a-zA-Z]+[a-zA-Z0-9._]+$/', 'message' => 'err.InvalidValue']),
                    ],
                ]
            );
        }

        $builder->add(
            'title',
            TextType::class,
            [
                'label' => $label,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'media_item';
    }
}
