<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Response;

final class PageDataType extends AbstractType
{
    /** @var PageDataTypeInterface[] */
    private const FORM_TYPES = [
        'redirect' => PageDataRedirectType::class,
        'hreflang' => PageDataHreflangType::class,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function (?array $persistedData): array {
                    $transformedData = [];
                    foreach (self::FORM_TYPES as $formTypeClass) {
                        $transformedData = $formTypeClass::transform($persistedData, $transformedData);
                    }

                    return $transformedData;
                },
                static function (array $submittedData): array {
                    $transformedData = [];
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
}
