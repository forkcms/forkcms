<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Common\Form\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageDataType extends AbstractType
{
    /** @var PageDataTypeInterface[] */
    private const FORM_TYPES = [
        'redirect' => PageDataRedirectType::class,
        'hreflang' => PageDataHreflangType::class,
        'authentication' => PageDataAuthenticationType::class,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'isAction',
            SwitchType::class,
            [
                'label' => 'msg.IsAction',
                'required' => false,
            ]
        );

        $builder->addModelTransformer(
            new CallbackTransformer(
                static function (?array $persistedData): array {
                    $transformedData = ['isAction' => $persistedData['is_action'] ?? false];
                    foreach (self::FORM_TYPES as $formTypeClass) {
                        $transformedData = $formTypeClass::transform($persistedData, $transformedData);
                    }

                    return $transformedData;
                },
                static function (array $submittedData): array {
                    $transformedData = ['is_action' => $submittedData['isAction']];
                    foreach (self::FORM_TYPES as $formTypeClass) {
                        $transformedData = $formTypeClass::reverseTransform($submittedData, $transformedData);
                    }

                    return $transformedData;
                }
            )
        );

        foreach (self::FORM_TYPES as $name => $class) {
            $builder->add($name, $class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'label' => false,
            ]
        );
    }
}
