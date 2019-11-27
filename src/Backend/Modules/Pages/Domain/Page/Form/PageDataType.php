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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function (?array $persistedData): array {
                    $data = [
                        'redirect' => 'none',
                        'internal_redirect' => null,
                        'external_redirect' => null,
                    ];

                    if ($persistedData === null) {
                        return $data;
                    }

                    if (isset($persistedData['internal_redirect']['page_id'])) {
                        $data['redirect'] = 'internal';
                        $data['external_redirect'] = $persistedData['internal_redirect']['page_id'];
                    } elseif (isset($persistedData['external_redirect']['url'])) {
                        $data['redirect'] = 'external';
                        $data['external_redirect'] = $persistedData['external_redirect']['url'];
                    }

                    return $data;
                },
                static function ($submittedData): array {
                    $data = [];
                    if ($submittedData['redirect'] === 'internal') {
                        $data['internal_redirect'] = [
                            'page_id' => $submittedData['internal_redirect'],
                            'code' => Response::HTTP_TEMPORARY_REDIRECT,
                        ];
                    } elseif ($submittedData['redirect'] === 'external') {
                        $data['external_redirect'] = [
                            'url' => $submittedData['external_redirect'],
                            'code' => Response::HTTP_TEMPORARY_REDIRECT,
                        ];
                    }

                    return $data;
                }
            )
        );
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
                'expanded' => true,
                'attr' => [
                    'class' => 'radiobuttonFieldCombo'
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
                    'class' => 'sr-only',
                ],
            ]
        );
        $builder->add(
            'external_redirect',
            UrlType::class,
            [
                'label' => 'lbl.ExternalLink',
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ]
        );
    }
}
