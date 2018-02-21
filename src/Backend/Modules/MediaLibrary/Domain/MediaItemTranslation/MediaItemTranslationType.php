<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItemTranslation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaItemTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $mediaItem = $event->getForm()->getParent()->getParent()->getData();
                    $label = 'lbl.MediaItemTitle';

                    if ($mediaItem->getMediaItemEntity()->getType()->isMovie()) {
                        $label = 'lbl.MediaMovieTitle';
                    }

                    $event->getForm()->add(
                        'title',
                        TextType::class,
                        [
                            'label' => $label,
                        ]
                    );
                }
            )
            ->add(
                'caption',
                TextareaType::class,
                [
                    'label' => 'lbl.MediaItemCaption',
                    'required' => false,
                ]
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $mediaItem = $event->getForm()->getParent()->getParent()->getData();

                    if (!$mediaItem->getMediaItemEntity()->getType()->isImage()) {
                        return;
                    }

                    $event->getForm()->add(
                        'hasCaptionLink',
                        CheckboxType::class,
                        [
                            'label' => 'msg.MediaItemHasCaptionLink',
                            'required' => false,
                        ]
                    );

                    $event->getForm()->add(
                        'captionLink',
                        TextType::class,
                        [
                            'label' => 'lbl.MediaItemCaptionLink',
                            'required' => false,
                        ]
                    );

                    $event->getForm()->add(
                        'alt',
                        TextType::class,
                        [
                            'label' => 'lbl.MediaItemAlt',
                            'required' => false,
                        ]
                    );
                }
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'lbl.MediaItemDescription',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaItemTranslationDataTransferObject::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->hasCaptionLink !== true) {
                    return ['Default'];
                }

                return ['Default', 'caption_link_is_required'];
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'media_library_media_item_translation';
    }
}
