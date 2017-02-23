<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Backend\Core\Engine\TemplateModifiers;
use Backend\Form\Type\EditorType;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\CreateMediaGallery;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;

class MediaGalleryType extends AbstractType
{
    /** @var string */
    private $dataClass;

    /** @var array $possibleWidgets */
    private $possibleWidgets;

    /**
     * MediaGalleryType constructor.
     *
     * @param array $possibleWidgets
     * @param string $dataClass
     */
    public function __construct($possibleWidgets, $dataClass = CreateMediaGallery::class)
    {
        $this->dataClass = $dataClass;
        $this->possibleWidgets = $possibleWidgets;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'lbl.Title',
                ]
            )
            ->add(
                'text',
                EditorType::class,
                [
                    'label' => 'lbl.Text',
                    'required' => false,
                ]
            )
            ->add(
                'action',
                ChoiceType::class,
                [
                    'label' => 'lbl.Action',
                    'choices' => $this->possibleWidgets,
                    'choices_as_values' => true,
                    'choice_label' => function ($possibleWidget) {
                        return $possibleWidget;
                    },
                    'choice_translation_domain' => false,
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'lbl.Status',
                    'choices' => Status::getPossibleValues(),
                    'choices_as_values' => true,
                    'choice_label' => function ($status) {
                        return TemplateModifiers::toLabel($status);
                    },
                    'choice_translation_domain' => false,
                ]
            )
            ->add(
                'publishOn',
                DateTimeType::class,
                [
                    'label' => 'lbl.PublishOn',
                ]
            )
            ->add(
                'mediaGroup',
                MediaGroupType::class,
                [
                    'label' => 'lbl.MediaConnected',
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => $this->dataClass]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_gallery';
    }
}
