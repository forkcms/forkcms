<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Core\Language\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'msg.ChooseTypeForNewGallery',
                    'choices' => array_map(
                        function ($type) {
                            return Type::fromString($type);
                        },
                        Type::POSSIBLE_VALUES
                    ),
                    'choices_as_values' => true,
                    'choice_label' => function (Type $type) {
                        return Language::lbl('MediaLibraryGroupType' . \SpoonFilter::toCamelCase($type, '-'), 'Core');
                    },
                    'choice_translation_domain' => false,
                    'choice_value' => function (Type $type = null) {
                        return (string) $type;
                    },
                    'data' => Type::fromString('image'),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_group_type';
    }
}
