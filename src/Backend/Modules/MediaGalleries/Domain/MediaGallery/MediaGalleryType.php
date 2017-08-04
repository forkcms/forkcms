<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Backend\Core\Engine\Authentication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Backend\Core\Engine\TemplateModifiers;
use Backend\Form\Type\EditorType;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Command\CreateMediaGallery;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\MediaLibrary\ValueObject\MediaWidget;
use Symfony\Component\Validator\Constraints\Valid;

class MediaGalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

        // You can only choose the "widget action" on "Add",
        // or always if you got "EditWidgetAction" rights or if you created the MediaGallery.
        if ($this->showFieldForWidgetAction($builder, $options)) {
            $builder->add(
                'action',
                ChoiceType::class,
                [
                    'label' => 'lbl.WidgetView',
                    'choices' => MediaWidget::getPossibleValues(),
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
                'mediaGroup',
                MediaGroupType::class,
                [
                    'label' => 'lbl.MediaConnected',
                    'constraints' => [new Valid()],
                    'required' => false,
                ]
            );
    }

    public function showFieldForWidgetAction(FormBuilderInterface $builder, array $options): bool
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
        return Authentication::isAllowedAction('MediaGalleryEditWidgetAction');
    }

    public function getBlockPrefix(): string
    {
        return 'media_gallery';
    }
}
