<?php

namespace ForkCMS\Core\Domain\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldsetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options['fields']($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(
                [
                    'card' => true,
                    'inherit_data' => true,
                    'fields' => static function (FormBuilderInterface $builder): void {
                    },
                    'label' => false,
                ]
            )
            ->addAllowedTypes('fields', 'callable');
        $resolver->setNormalizer('row_attr', static function (Options $options, $value) {
            if ($options['card']) {
                $value['class'] = $value['class'] ?? '';
                $value['class'] .= ' card card-default';
            }

            return $value;
        });
        $resolver->setNormalizer('label_attr', static function (Options $options, $value) {
            if ($options['card']) {
                $value['class'] = $value['class'] ?? '';
                $value['class'] .= ' card-header';
            }

            return $value;
        });
        $resolver->setNormalizer('attr', static function (Options $options, $value) {
            if ($options['card']) {
                $value['class'] = $value['class'] ?? '';
                $value['class'] .= ' card-body';
            }

            return $value;
        });
    }
}
