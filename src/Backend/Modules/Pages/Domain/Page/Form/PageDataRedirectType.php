<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageDataRedirectType extends AbstractType implements PageDataTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'redirect',
            ChoiceType::class,
            [
                'label' => 'lbl.Redirect',
                'choices' => [
                    'lbl.None' => 'none',
                    'lbl.InternalLink' => 'internal',
                    'lbl.ExternalLink' => 'external',
                ],
                'label_attr' => [
                    'class' => 'custom-control-label radio-custom',
                ],
                'expanded' => true,
                'attr' => [
                    'class' => 'radiobuttonFieldCombo',
                ],
            ]
        );
        $builder->add(
            'internal_redirect',
            ChoiceType::class,
            [
                'choices' => array_flip(BackendPagesModel::getPagesForDropdown()),
                'label' => 'lbl.InternalLink',
                'label_attr' => [
                    'class' => 'visually-hidden',
                ],
            ]
        );
        $builder->add(
            'external_redirect',
            UrlType::class,
            [
                'label' => 'lbl.ExternalLink',
                'label_attr' => [
                    'class' => 'visually-hidden',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'inherit_data' => true,
            ]
        );
    }

    public static function transform(?array $persistedData, array $transformedData): array
    {
        $transformedData['redirect'] = 'none';
        $transformedData['internal_redirect'] = null;
        $transformedData['external_redirect'] = null;

        if ($persistedData === null) {
            return $transformedData;
        }

        if (isset($persistedData['internal_redirect']['page_id'])) {
            $transformedData['redirect'] = 'internal';
            $transformedData['external_redirect'] = $persistedData['internal_redirect']['page_id'];

            return $transformedData;
        }

        if (isset($persistedData['external_redirect']['url'])) {
            $transformedData['redirect'] = 'external';
            $transformedData['external_redirect'] = $persistedData['external_redirect']['url'];

            return $transformedData;
        }

        return $transformedData;
    }

    public static function reverseTransform(array $submittedData, array $transformedData): array
    {
        if ($submittedData['redirect'] === 'internal') {
            $transformedData['internal_redirect'] = [
                'page_id' => $submittedData['internal_redirect'],
                'code' => Response::HTTP_TEMPORARY_REDIRECT,
            ];

            return $transformedData;
        }


        if ($submittedData['redirect'] === 'external') {
            $transformedData['external_redirect'] = [
                'url' => $submittedData['external_redirect'],
                'code' => Response::HTTP_TEMPORARY_REDIRECT,
            ];

            return $transformedData;
        }

        return $transformedData;
    }
}
