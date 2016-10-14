<?php

namespace Backend\Form\Type;

use Common\Doctrine\Entity\Meta;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                ['label' => 'lbl.PageTitle', 'label_attr' => ['class' => 'sr-only']]
            )
            ->add(
                'titleOverwrite',
                CheckboxType::class,
                ['label' => 'lbl.PageTitle', 'required' => false]
            )
            ->add(
                'description',
                TextType::class,
                ['label' => 'lbl.Description', 'label_attr' => ['class' => 'sr-only']]
            )
            ->add(
                'descriptionOverwrite',
                CheckboxType::class,
                ['label' => 'lbl.Description', 'required' => false]
            )
            ->add(
                'keywords',
                TextType::class,
                ['label' => 'lbl.Keywords', 'label_attr' => ['class' => 'sr-only']]
            )
            ->add(
                'keywordsOverwrite',
                CheckboxType::class,
                ['label' => 'lbl.Keywords', 'required' => false]
            )
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
            ->add(
                'SEOIndex',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => array_combine(
                        SEOIndex::getPossibleValues(),
                        SEOIndex::getPossibleValues()
                    ),
                    'choice_label' => function ($SEOIndex) {
                        if ($SEOIndex === SEOIndex::NONE) {
                            return 'lbl.' . ucfirst($SEOIndex);
                        }

                        return $SEOIndex;
                    },
                    'data' => SEOIndex::NONE,
                    'choice_translation_domain' => true,
                    'required' => false,
                    'placeholder' => false,
                    'label_attr' => ['class' => 'radio-list'],
                ]
            )
            ->add(
                'SEOFollow',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => array_combine(
                        SEOFollow::getPossibleValues(),
                        SEOFollow::getPossibleValues()
                    ),
                    'choice_label' => function ($SEOFollow) {
                        if ($SEOFollow === SEOFollow::NONE) {
                            return 'lbl.' . ucfirst($SEOFollow);
                        }

                        return $SEOFollow;
                    },
                    'data' => SEOFollow::NONE,
                    'choice_translation_domain' => true,
                    'required' => false,
                    'placeholder' => false,
                    'label_attr' => ['class' => 'radio-list'],
                ]
            )
            ->addModelTransformer(
                new CallbackTransformer($this->getMetaTransformFunction(), $this->getMetaReverseTransformFunction())
            );

        if ($options['custom_meta_tags']) {
            $builder->add(
                'custom',
                TextareaType::class,
                ['label' => 'lbl.ExtraMetaTags', 'required' => false, 'attr' => ['rows' => 5, 'cols' => 62]]
            );
        }
    }

    private function getMetaTransformFunction()
    {
        return function ($meta) {
            if (!$meta instanceof Meta) {
                return;
            }

            return [
                'title' => $meta->getTitle(),
                'titleOverwrite' => $meta->isTitleOverwrite(),
                'description' => $meta->getDescription(),
                'descriptionOverwrite' => $meta->isDescriptionOverwrite(),
                'keywords' => $meta->getKeywords(),
                'keywordsOverwrite' => $meta->isKeywordsOverwrite(),
                'custom' => $meta->getCustom(),
                'url' => $meta->getUrl(),
                'urlOverwrite' => $meta->isUrlOverwrite(),
                'SEOIndex' => $meta->getSEOIndex(),
                'SEOFollow' => $meta->getSEOFollow(),
            ];
        };
    }

    private function getMetaReverseTransformFunction()
    {
        return function ($metaData) {
            return Meta::fromFormData($metaData);
        };
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['base_field_name', 'custom_meta_tags']);
        $resolver->setDefaults(
            [
                'label' => false,
                'custom_meta_tags' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'meta';
    }
}
