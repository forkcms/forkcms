<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Backend\Core\Engine\Authentication;
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
use Backend\Modules\MediaLibrary\ValueObject\MediaWidget;

class MediaGalleryType extends AbstractType
{
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
                    'label' => 'lbl.Description',
                    'required' => false,
                ]
            );

        // You can only choose the "widget action" on "Add", or always if you got "EditWidgetAction" rights or if you created the MediaGallery.
        if ($this->showFieldForWidgetAction($builder, $options)) {
            $builder->add(
                'action',
                ChoiceType::class,
                [
                    'label' => 'lbl.Action',
                    'choices' => MediaWidget::getPossibleValues(),
                    'choices_as_values' => true,
                    'choice_label' => function ($possibleWidget) {
                        return $possibleWidget;
                    },
                    'choice_translation_domain' => false,
                ]
            );
        }

        $builder
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'lbl.Status',
                    'choices' => array_map(
                        function ($status) {
                            return Status::fromString($status);
                        },
                        Status::POSSIBLE_VALUES
                    ),
                    'choices_as_values' => true,
                    'choice_label' => function (Status $type) {
                        return TemplateModifiers::toLabel($type);
                    },
                    'choice_translation_domain' => false,
                    'choice_value' => function (Status $status = null) {
                        return (string) $status;
                    },
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
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return bool
     */
    public function showFieldForWidgetAction(FormBuilderInterface $builder, array $options)
    {
        // You can always see the widgetAction field in the "CreateMediaGallery" command
        if ($options['data_class'] === CreateMediaGallery::class) {
            return true;
        }

        // When it is your gallery, you can see the widgetAction field
        if ($builder->getData()->userId === Authentication::getUser()->getUserId()) {
            return true;
        }

        // Otherwise, when you have the rights, you can edit the widgetAction
        if (Authentication::isAllowedAction('MediaGalleryEditWidgetAction')) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_gallery';
    }
}
