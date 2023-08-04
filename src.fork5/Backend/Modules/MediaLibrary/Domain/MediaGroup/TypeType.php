<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Core\Language\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'msg.ChooseTypeForNewGroup',
                    'choices' => $this->getTypeChoices(),
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

    public function getBlockPrefix(): string
    {
        return 'media_group_type';
    }

    private function getTypeChoices(): array
    {
        return array_map(
            function ($type) {
                return Type::fromString($type);
            },
            Type::POSSIBLE_VALUES
        );
    }
}
