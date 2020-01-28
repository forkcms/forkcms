<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Core\Language\Language;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Common\Core\Model;
use Common\Form\SwitchType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use SpoonFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Backend\Form\Type\EditorType;

final class PageBlockType extends AbstractType
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'extraType',
            ChoiceType::class,
            [
                'label' => 'lbl.Type',
                'choices' => $options['possibleExtraTypes'],
                'attr' => [
                    'data-role' => 'select-block-type',
                ],
                'choice_value' => static function (?Type $type): ?string {
                    if ($type === null) {
                        return null;
                    }

                    return (string) $type;
                },
            ]
        );
        $builder->add(
            'visible',
            SwitchType::class,
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
            'extraId', // will be filled in the event afterwards
            HiddenType::class,
            [
                'required' => false,
            ]
        );
        $builder->add(
            'html',
            EditorType::class,
            [
                'label' => 'lbl.Content',
                'required' => false,
            ]
        );
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            static function (FormEvent $event): void {
                $data = $event->getData();
                $data['sequence'] = (int) $data['sequence'];
                $type = new Type($data['extraType']);
                $data['extraId'] = null;
                if ($type->isBlock() || $type->isWidget()) {
                    $data['extraId'] = (int) $data[$type . 'ExtraId'];
                }

                $event->setData($data);
            }
        );
        $entityManager = $this->entityManager;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($entityManager): void {
                $data = $event->getData();
                $extraId = null;
                if ($data instanceof PageBlockDataTransferObject) {
                    $extraId = $data->extraId;
                }

                $moduleExtra = null;
                if ($extraId !== null) {
                    $moduleExtra = $entityManager->getReference(ModuleExtra::class, $extraId);
                }
                $event->getForm()->add(
                    'widgetExtraId',
                    EntityType::class,
                    self::getModuleExtraOptions(Type::widget(), $moduleExtra)
                );
                $event->getForm()->add(
                    'blockExtraId',
                    EntityType::class,
                    self::getModuleExtraOptions(Type::block(), $moduleExtra)
                );
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PageBlockDataTransferObject::class]);
        $resolver->setRequired('possibleExtraTypes');
    }

    private static function getModuleExtraOptions(Type $type, ?ModuleExtra $moduleExtra): array
    {
        return [
            'data' => $moduleExtra,
            'label' => $type->getLabel(),
            'class' => ModuleExtra::class,
            'choice_label' => static function (ModuleExtra $moduleExtra): string {
                return $moduleExtra->getTranslatedLabel();
            },
            'group_by' => static function (ModuleExtra $moduleExtra): string {
                return SpoonFilter::ucfirst(Language::lbl($moduleExtra->getModule()));
            },
            'query_builder' => static function (ModuleExtraRepository $repository) use ($type): QueryBuilder {
                return $repository
                    ->createQueryBuilder('me')
                    ->where('me.type = :type')
                    ->setParameter('type', $type)
                    ->andWhere('me.id IN (:allowedExtraIds)')
                    ->setParameter('allowedExtraIds', array_keys(BackendExtensionsModel::getExtras()))
                    ->orderBy('me.sequence');
            },
            'mapped' => false,
            'attr' => [
                'data-fork' => 'select2',
            ],
        ];
    }
}
