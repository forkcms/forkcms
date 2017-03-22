<?php

namespace Backend\Form\Type;

use Closure;
use Common\Doctrine\Entity\Meta;
use Common\Doctrine\Repository\MetaRepository;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use SpoonFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class MetaType extends AbstractType
{
    /** @var MetaRepository */
    private $metaRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Meta[] */
    private $meta;

    /**
     * @param MetaRepository $metaRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(MetaRepository $metaRepository, TranslatorInterface $translator)
    {
        $this->metaRepository = $metaRepository;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                HiddenType::class
            )
            ->add(
                'title',
                TextType::class,
                ['label' => 'lbl.PageTitle', 'label_attr' => ['class' => 'sr-only']]
            )
            ->add('titleOverwrite', CheckboxType::class, ['label' => 'lbl.PageTitle', 'required' => false])
            ->add(
                'description',
                TextType::class,
                ['label' => 'lbl.Description', 'label_attr' => ['class' => 'sr-only']]
            )
            ->add('descriptionOverwrite', CheckboxType::class, ['label' => 'lbl.Description', 'required' => false])
            ->add(
                'keywords',
                TextType::class,
                ['label' => 'lbl.Keywords', 'label_attr' => ['class' => 'sr-only']]
            )
            ->add('keywordsOverwrite', CheckboxType::class, ['label' => 'lbl.Keywords', 'required' => false])
            ->add(
                'url',
                TextType::class,
                [
                    'attr' => ['class' => 'fork-form-text'],
                    'label' => 'lbl.URL',
                    'label_attr' => ['class' => 'sr-only'],
                ]
            )
            ->add('urlOverwrite', CheckboxType::class, ['label' => 'lbl.URL', 'required' => false])
            ->add('SEOIndex', ChoiceType::class, $this->getSEOIndexChoiceTypeOptions())
            ->add('SEOFollow', ChoiceType::class, $this->getSEOFollowChoiceTypeOptions())
            ->addModelTransformer(
                new CallbackTransformer($this->getMetaTransformFunction(), $this->getMetaReverseTransformFunction())
            )
            ->addEventListener(FormEvents::SUBMIT, $this->getSubmitEventFunction($options['base_field_name']));

        if ($options['custom_meta_tags']) {
            $builder->add(
                'custom',
                TextareaType::class,
                ['label' => 'lbl.ExtraMetaTags', 'required' => false, 'attr' => ['rows' => 5, 'cols' => 62]]
            );
        }
    }

    /**
     * @return array
     */
    private function getSEOIndexChoiceTypeOptions()
    {
        return [
            'expanded' => true,
            'multiple' => false,
            'choices' => array_map(
                function ($SEOIndex) {
                    return SEOIndex::fromString($SEOIndex);
                },
                SEOIndex::getPossibleValues()
            ),
            'choices_as_values' => true,
            'choice_value' => function (SEOIndex $SEOIndex = null) {
                return (string) $SEOIndex;
            },
            'choice_label' => function ($SEOIndex) {
                if ($SEOIndex->isNone()) {
                    return 'lbl.' . ucfirst($SEOIndex);
                }

                return $SEOIndex;
            },
            'choice_translation_domain' => true,
            'required' => false,
            'placeholder' => false,
            'label_attr' => ['class' => 'radio-list'],
        ];
    }

    /**
     * @return array
     */
    private function getSEOFollowChoiceTypeOptions()
    {
        return [
            'expanded' => true,
            'multiple' => false,
            'choices' => array_map(
                function ($SEOFollow) {
                    return SEOFollow::fromString($SEOFollow);
                },
                SEOFollow::getPossibleValues()
            ),
            'choices_as_values' => true,
            'choice_value' => function (SEOFollow $SEOFollow = null) {
                return (string) $SEOFollow;
            },
            'choice_label' => function ($SEOFollow) {
                if ($SEOFollow->isNone()) {
                    return 'lbl.' . ucfirst($SEOFollow);
                }

                return $SEOFollow;
            },
            'choice_translation_domain' => true,
            'required' => false,
            'placeholder' => false,
            'label_attr' => ['class' => 'radio-list'],
        ];
    }

    /**
     * @param string $baseFieldName
     *
     * @return Closure
     */
    private function getSubmitEventFunction($baseFieldName)
    {
        return function (FormEvent $event) use ($baseFieldName) {
            $metaForm = $event->getForm();
            $metaData = $event->getData();
            $parentForm = $metaForm->getParent();
            if ($parentForm === null) {
                throw new LogicException(
                    'The MetaType is not a stand alone type, it needs to be used in a parent form'
                );
            }

            if (!$parentForm->has($baseFieldName)) {
                throw new InvalidArgumentException('The base_field_name does not exist in the parent form');
            }

            $defaultValue = $parentForm->get($baseFieldName)->getData();

            $overwritableFields = $this->getOverwritableFields();
            array_walk(
                $overwritableFields,
                function ($fieldName) use ($metaForm, $defaultValue, &$metaData) {
                    if ($metaForm->get($fieldName . 'Overwrite')->getData()) {
                        // we are overwriting it so we don't need to set the fallback
                        return;
                    }

                    $metaData[$fieldName] = $defaultValue;
                }
            );

            $generatedUrl = $this->metaRepository->generateURL(
                SpoonFilter::htmlspecialcharsDecode($metaData['url']),
                $metaForm->getConfig()->getOption('generate_url_callback_class'),
                $metaForm->getConfig()->getOption('generate_url_callback_method'),
                $metaForm->getConfig()->getOption('generate_url_callback_parameters')
            );

            if ($generatedUrl !== $metaData['url'] && $metaData['urlOverwrite']) {
                $metaForm->get('url')->addError(new FormError($this->translator->trans('err.URLAlreadyExists')));
                $event->setData($metaData);

                return;
            }

            $metaData['url'] = $generatedUrl;
            $event->setData($metaData);
        };
    }

    protected function getOverwritableFields()
    {
        return ['title', 'description', 'keywords', 'url'];
    }

    /**
     * @return Closure
     */
    private function getMetaTransformFunction()
    {
        return function ($meta) {
            if (!$meta instanceof Meta) {
                return [
                    'SEOIndex' =>  SEOIndex::none(),
                    'SEOFollow' => SEOFollow::none(),
                ];
            }

            $this->meta[$meta->getId()] = $meta;

            return [
                'id' => $meta->getId(),
                'title' => $meta->getTitle(),
                'titleOverwrite' => $meta->isTitleOverwrite(),
                'description' => $meta->getDescription(),
                'descriptionOverwrite' => $meta->isDescriptionOverwrite(),
                'keywords' => $meta->getKeywords(),
                'keywordsOverwrite' => $meta->isKeywordsOverwrite(),
                'custom' => $meta->getCustom(),
                'url' => $meta->getUrl(),
                'urlOverwrite' => $meta->isUrlOverwrite(),
                'SEOIndex' => $meta->getSEOIndex() === null ? SEOIndex::none() : $meta->getSEOIndex(),
                'SEOFollow' => $meta->getSEOFollow() === null ? SEOFollow::none() : $meta->getSEOFollow(),
            ];
        };
    }

    /**
     * @return Closure
     */
    private function getMetaReverseTransformFunction()
    {
        return function ($metaData) {
            $metaId = $metaData['id'] === null ? null : (int) $metaData['id'];

            if ($metaId === null || !$this->meta[$metaId] instanceof Meta) {
                return new Meta(
                    $metaData['keywords'],
                    $metaData['keywordsOverwrite'],
                    $metaData['description'],
                    $metaData['descriptionOverwrite'],
                    $metaData['title'],
                    $metaData['titleOverwrite'],
                    $metaData['url'],
                    $metaData['urlOverwrite'],
                    array_key_exists('custom', $metaData) ? $metaData['custom'] : null,
                    [
                        'seo_index' => SEOIndex::fromString($metaData['SEOIndex']),
                        'seo_follow' => SEOFollow::fromString($metaData['SEOFollow']),
                    ],
                    $metaData['id'] = $metaId
                );
            }

            $this->meta[$metaId]->update(
                $metaData['keywords'],
                $metaData['keywordsOverwrite'],
                $metaData['description'],
                $metaData['descriptionOverwrite'],
                $metaData['title'],
                $metaData['titleOverwrite'],
                $metaData['url'],
                $metaData['urlOverwrite'],
                array_key_exists('custom', $metaData) ? $metaData['custom'] : null,
                [
                    'seo_index' => SEOIndex::fromString($metaData['SEOIndex']),
                    'seo_follow' => SEOFollow::fromString($metaData['SEOFollow']),
                ]
            );

            return $this->meta[$metaId];
        };
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'base_field_name',
                'custom_meta_tags',
                'generated_url_selector',
                'generate_url_callback_class',
                'generate_url_callback_method',
                'generate_url_callback_parameters',
            ]
        );
        $resolver->setDefaults(
            [
                'label' => false,
                'custom_meta_tags' => false,
                'generated_url_selector' => '#generatedUrl',
                'generate_url_callback_parameters' => [],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'meta';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($view->parent === null) {
            throw new LogicException(
                'The MetaType is not a stand alone type, it needs to be used in a parent form'
            );
        }

        if (!isset($view->parent->children[$options['base_field_name']])) {
            throw new InvalidArgumentException('The base_field_name does not exist in the parent form');
        }
        $view->vars['base_field_selector'] = '#' . $view->parent->children[$options['base_field_name']]->vars['id'];
        $view->vars['custom_meta_tags'] = $options['custom_meta_tags'];
        $view->vars['generate_url_callback_class'] = $options['generate_url_callback_class'];
        $view->vars['generate_url_callback_method'] = $options['generate_url_callback_method'];
        $view->vars['generate_url_callback_parameters'] = serialize($options['generate_url_callback_parameters']);
    }
}
