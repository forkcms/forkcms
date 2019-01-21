<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Modules\MediaLibrary\Domain\MediaItemTranslation\MediaItemTranslationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class MediaItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($builder->getData()->getMediaItemEntity()->getType()->isMovie()) {
            $builder->add(
                'url',
                TextType::class,
                [
                    'label' => 'lbl.MediaMovieId',
                ]
            );
        }

        $builder
            ->add(
                'translations',
                CollectionType::class,
                [
                    'entry_type' => MediaItemTranslationType::class,
                    'error_bubbling' => false,
                    'constraints' => [new Valid()],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => MediaItemDataTransferObject::class]);
    }

    public function getBlockPrefix(): string
    {
        return 'media_library_media_item';
    }
}
