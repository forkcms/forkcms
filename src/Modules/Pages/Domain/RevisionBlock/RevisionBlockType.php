<?php

namespace ForkCMS\Modules\Pages\Domain\RevisionBlock;

use Doctrine\ORM\QueryBuilder;
use ForkCMS\Core\Domain\Form\EditorType;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RevisionBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'blockType',
            ChoiceType::class,
            [
                'label' => 'lbl.Type',
                'choices' => $options['possibleExtraTypes'],
                'attr' => [
                    'data-role' => 'select-block-type',
                ],
                'choice_value' => static fn (?Type $type): ?string => $type?->value,
                'choice_label' => static fn (?Type $type): ?TranslationKey => $type?->getLabel(),
                'mapped' => false,
            ]
        );
        $builder->add(
            'isVisible',
            CheckboxType::class,
            [
                'label' => 'lbl.Visible',
                'required' => false,
            ]
        );
        $builder->add(
            'sequence',
            HiddenType::class,
            [
                'required' => false,
                'attr' => [
                    'data-role' => 'sequence',
                ],
            ]
        );
        $builder->add(
            'block',
            EntityType::class,
            [
                'label' => 'lbl.Block',
                'class' => Block::class,
                'required' => false,
                'group_by' => static fn (Block $block): TranslationKey => $block->getBlock()->getModule()->asLabel(),
                'choice_label' => static fn (Block $block): TranslationKey => $block->getLabel(),
                'choice_attr' => static fn (Block $block): array => [
                    'data-type' => $block->getType()->value,
                ],
                'query_builder' => static function (BlockRepository $repository): QueryBuilder {
                    return $repository->createQueryBuilder('b')
                        ->where('b.locale IS NULL OR b.locale = :locale')
                        ->setParameter('locale', Locale::current())
                        ->orderBy('b.position');
                },
                'attr' => [
                    'data-role' => 'select-block',
                ],
                'placeholder' => 'lbl.Editor',
            ]
        );
        $builder->add(
            'editorContent',
            EditorType::class,
            [
                'label' => 'lbl.Content',
                'required' => false,
            ]
        );
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            static function (FormEvent $event) use ($entityManager): void {
//                $data = $event->getData();
//                $extraId = null;
//                if ($data instanceof RevisionBlockDataTransferObject) {
//                    $extraId = $data->extraId;
//                }
//
//                $moduleExtra = null;
//                if ($extraId !== null) {
//                    $moduleExtra = $entityManager->getReference(ModuleExtra::class, $extraId);
//
//                    $event->getData()->extraModule = $moduleExtra->getModule();
//                    $event->getData()->extraLabel = $moduleExtra->getTranslatedLabel();
//                }
//                $event->getForm()->add(
//                    'widgetExtraId',
//                    EntityType::class,
//                    self::getModuleExtraOptions(Type::widget(), $moduleExtra)
//                );
//                $event->getForm()->add(
//                    'blockExtraId',
//                    EntityType::class,
//                    self::getModuleExtraOptions(Type::block(), $moduleExtra)
//                );
//            }
//        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => RevisionBlockDataTransferObject::class]);
        $resolver->setRequired('possibleExtraTypes');
    }
//
//    private static function getModuleExtraOptions(Type $type, ?ModuleExtra $moduleExtra): array
//    {
//        return [
//            'data' => $moduleExtra,
//            'label' => $type->getLabel(),
//            'class' => ModuleExtra::class,
//            'choice_label' => static function (ModuleExtra $moduleExtra): string {
//                return $moduleExtra->getTranslatedLabel();
//            },
//            'group_by' => static function (ModuleExtra $moduleExtra): string {
//                return SpoonFilter::ucfirst(Language::lbl($moduleExtra->getModule()));
//            },
//            'query_builder' => static function (ModuleExtraRepository $repository) use ($type): QueryBuilder {
//                return $repository
//                    ->createQueryBuilder('me')
//                    ->where('me.type = :type')
//                    ->setParameter('type', $type)
//                    ->andWhere('me.id IN (:allowedExtraIds)')
//                    ->setParameter('allowedExtraIds', array_keys(BackendExtensionsModel::getExtras()))
//                    ->orderBy('me.sequence');
//            },
//            'mapped' => false,
//            'attr' => [
//                'data-fork' => 'select2',
//            ],
//        ];
//    }
}
