<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $label = 'lbl.Title';

        if ($builder->getData()->getMediaItemEntity()->getType()->isMovie()) {
            $label = 'lbl.MediaMovieTitle';
            $this->addField($builder, 'url', 'lbl.MediaMovieId');
        }

        $this->addField($builder, 'title', $label);
    }

    private function addField(FormBuilderInterface $builder, string $name, string $label): void
    {
        $builder->add(
            $name,
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
