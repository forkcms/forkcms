<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use ForkCMS\Core\Domain\Form\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class MetaType extends AbstractType
{
    /** @var array<int, Meta> */
    private array $meta = [];

    public function __construct(
        private readonly MetaRepository $metaRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'id',
                HiddenType::class
            )
            ->add(
                'title',
                TextType::class,
                ['label' => 'lbl.PageTitle', 'label_attr' => ['class' => 'visually-hidden']]
            )
            ->add('titleOverwrite', SwitchType::class, ['label' => 'lbl.PageTitle', 'required' => false])
            ->add(
                'description',
                TextType::class,
                ['label' => 'lbl.Description', 'label_attr' => ['class' => 'visually-hidden']]
            )
            ->add('descriptionOverwrite', SwitchType::class, ['label' => 'lbl.Description', 'required' => false])
            ->add(
                'keywords',
                TextType::class,
                ['label' => 'lbl.Keywords', 'label_attr' => ['class' => 'visually-hidden']]
            )
            ->add('keywordsOverwrite', SwitchType::class, ['label' => 'lbl.Keywords', 'required' => false])
            ->add(
                'slug',
                TextType::class,
                [
                    'attr' => ['class' => 'fork-form-text'],
                    'label' => 'lbl.URL',
                    'label_attr' => ['class' => 'visually-hidden'],
                    'disabled' => $options['disable_slug_overwrite'],
                ]
            )
            ->add(
                'slugOverwrite',
                SwitchType::class,
                [
                    'label' => 'lbl.URL',
                    'required' => false,
                    'disabled' => $options['disable_slug_overwrite'],
                ]
            )
            ->add(
                'canonicalUrl',
                TextType::class,
                [
                    'attr' => ['class' => 'fork-form-text'],
                    'label' => 'lbl.CanonicalURL',
                    'label_attr' => ['class' => 'visually-hidden'],
                ]
            )
            ->add('canonicalUrlOverwrite', SwitchType::class, ['label' => 'lbl.CanonicalURL', 'required' => false])
            ->add('SEOIndex', EnumType::class, $this->getSEOIndexChoiceTypeOptions())
            ->add('SEOFollow', EnumType::class, $this->getSEOFollowChoiceTypeOptions())
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

    /** @return array<string, mixed> */
    private function getSEOIndexChoiceTypeOptions(): array
    {
        return [
            'expanded' => true,
            'multiple' => false,
            'class' => SEOIndex::class,
            'choice_label' => static fn (SEOIndex $SEOIndex) => 'lbl.' . ucfirst($SEOIndex->name),
            'choice_translation_domain' => true,
            'required' => false,
            'placeholder' => false,
            'label_attr' => ['class' => 'custom-control-label radio-custom'],
        ];
    }

    /** @return array<string, mixed> */
    private function getSEOFollowChoiceTypeOptions(): array
    {
        return [
            'expanded' => true,
            'multiple' => false,
            'class' => SEOFollow::class,
            'choice_label' => static fn (SEOFollow $SEOFollow) => 'lbl.' . ucfirst($SEOFollow->name),
            'choice_translation_domain' => true,
            'required' => false,
            'placeholder' => false,
            'label_attr' => ['class' => 'custom-control-label radio-custom'],
        ];
    }

    private function getSubmitEventFunction(string $baseFieldName): callable
    {
        return function (FormEvent $event) use ($baseFieldName) {
            $metaForm = $event->getForm();
            $metaData = $event->getData();
            $parent = $metaForm->getParent();
            if ($parent === null) {
                throw new LogicException(
                    'The MetaType is not a stand alone type, it needs to be used in a parent form'
                );
            }

            $baseField = null;
            while ($parent !== null && $baseField === null) {
                $baseField = $parent->has($baseFieldName) ? $parent->get($baseFieldName) : null;
                $parent = $parent->getParent();
            }
            if ($baseField === null) {
                throw new InvalidArgumentException('The base_field_name does not exist in the parent form');
            }

            $defaultValue = $baseField->getData();

            $overwritableFields = $this->getOverwritableFields();
            array_walk(
                $overwritableFields,
                static function ($fieldName) use ($metaForm, $defaultValue, &$metaData) {
                    if ($metaForm->has($fieldName) && $metaForm->get($fieldName . 'Overwrite')->getData()) {
                        // we are overwriting it so we don't need to set the fallback
                        return;
                    }

                    $metaData[$fieldName] = $defaultValue;
                }
            );

            $generatedSlug = $this->metaRepository->generateSlug(
                htmlspecialchars_decode($metaData['slug']),
                $metaForm->getConfig()->getOption('generate_slug_callback_class'),
                $metaForm->getConfig()->getOption('generate_slug_callback_method'),
                $metaForm->getConfig()->getOption('generate_slug_callback_parameters')
            );

            if ($generatedSlug !== $metaData['slug'] && $metaData['slugOverwrite']) {
                $metaForm->get('slug')->addError(
                    new FormError($this->translator->trans(self::getInvalidUrlErrorMessage($generatedSlug)))
                );
                $event->setData($metaData);

                return;
            }

            $metaData['slug'] = $generatedSlug;
            $event->setData($metaData);
        };
    }

    /** @return string[] */
    protected function getOverwritableFields(): array
    {
        return ['title', 'navigationTitle', 'description', 'keywords', 'slug'];
    }

    private function getMetaTransformFunction(): callable
    {
        return function ($meta) {
            if (!$meta instanceof Meta) {
                return [
                    'SEOIndex' => SEOIndex::none,
                    'SEOFollow' => SEOFollow::none,
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
                'slug' => $meta->getSlug(),
                'slugOverwrite' => $meta->isSlugOverwrite(),
                'canonicalUrl' => $meta->getCanonicalUrl(),
                'canonicalUrlOverwrite' => $meta->isCanonicalUrlOverwrite(),
                'SEOIndex' => $meta->getSEOIndex(),
                'SEOFollow' => $meta->getSEOFollow(),
            ];
        };
    }

    private function getMetaReverseTransformFunction(): callable
    {
        return function ($metaData) {
            $metaId = $metaData['id'] === null ? null : (int) $metaData['id'];

            if ($metaId === null || !$this->meta[$metaId] instanceof Meta) {
                return new Meta(
                    (string) $metaData['keywords'],
                    $metaData['keywordsOverwrite'],
                    (string) $metaData['description'],
                    $metaData['descriptionOverwrite'],
                    (string) $metaData['title'],
                    $metaData['titleOverwrite'],
                    (string) $metaData['slug'],
                    $metaData['slugOverwrite'],
                    $metaData['canonicalUrl'],
                    $metaData['canonicalUrlOverwrite'],
                    $metaData['custom'] ?? null,
                    $metaData['SEOFollow'],
                    $metaData['SEOIndex'],
                    null,
                    $metaId
                );
            }

            $this->meta[$metaId]->update(
                $metaData['keywords'],
                $metaData['keywordsOverwrite'],
                $metaData['description'],
                $metaData['descriptionOverwrite'],
                $metaData['title'],
                $metaData['titleOverwrite'],
                $metaData['slug'],
                $metaData['slugOverwrite'],
                $metaData['canonicalUrl'],
                $metaData['canonicalUrlOverwrite'],
                $metaData['custom'] ?? null,
                $metaData['SEOFollow'],
                $metaData['SEOIndex']
            );

            return $this->meta[$metaId];
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(
            [
                'base_field_name',
                'custom_meta_tags',
                'generate_slug_callback_class',
                'base_url',
            ]
        );
        $resolver->setDefaults(
            [
                'label' => false,
                'custom_meta_tags' => false,
                'generated_slug_selector' => '#generatedSlug',
                'generate_slug_callback_method' => 'slugify',
                'generate_slug_callback_parameters' => [],
                'disable_slug_overwrite' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'meta';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($view->parent === null) {
            throw new LogicException('The MetaType is not a stand alone type, it needs to be used in a parent form');
        }

        $parent = $view->parent;
        $baseField = null;
        while ($parent !== null && $baseField === null) {
            $baseField = $parent->children[$options['base_field_name']] ?? null;
            $parent = $parent->parent;
        }
        if ($baseField === null) {
            throw new InvalidArgumentException('The base_field_name does not exist in the parent form');
        }
        $view->vars['base_field_selector'] = '#' . $baseField->vars['id'];
        $view->vars['custom_meta_tags'] = $options['custom_meta_tags'];
        $view->vars['generate_slug_callback_class'] = $options['generate_slug_callback_class'];
        $view->vars['generate_slug_callback_method'] = $options['generate_slug_callback_method'];
        $view->vars['generated_slug_selector'] = $options['generated_slug_selector'];
        $view->vars['generate_slug_callback_parameters'] = serialize($options['generate_slug_callback_parameters']);
        $view->vars['base_url'] = $options['base_url'];
    }

    private static function stripNumberAddedByTheUrlGeneration(string $string): string
    {
        $chunks = explode('-', $string);

        if (!is_numeric(end($chunks))) {
            return $string;
        }

        // remove last chunk
        array_pop($chunks);

        return implode('-', $chunks);
    }

    private static function getInvalidUrlErrorMessage(string $generatedSlug): string
    {
        $baseGeneratedUrl = self::stripNumberAddedByTheUrlGeneration($generatedSlug);

        if ($baseGeneratedUrl !== $generatedSlug && str_starts_with($generatedSlug, $baseGeneratedUrl)) {
            return 'err.URLAlreadyExists';
        }

        return 'err.InvalidURL';
    }
}
