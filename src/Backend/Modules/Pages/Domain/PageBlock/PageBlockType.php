<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Core\Language\Language;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Common\Form\SwitchType;
use Doctrine\ORM\QueryBuilder;
use SpoonFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Backend\Form\Type\EditorType;

final class PageBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'extraType',
            ChoiceType::class,
            [
                'label' => 'lbl.Type',
                'choices' => Type::dropdownChoices(),
                'attr' => [
                    'data-role' => 'select-block-type',
                ],
                'choice_value' => static function (Type $type): string {
                    return (string) $type;
                }
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
            'widgetExtraId',
            EntityType::class,
            $this->getModuleExtraOptions(Type::widget())
        );
        $builder->add(
            'moduleExtraId',
            EntityType::class,
            $this->getModuleExtraOptions(Type::block())
        );
        $builder->add(
            'position',
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PageBlockDataTransferObject::class]);
    }

    private function getModuleExtraOptions(Type $type): array
    {
        return [
            'label' => $type->getLabel(),
            'class' => ModuleExtra::class,
            'choice_label' => static function (ModuleExtra $moduleExtra) : string {
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
            'attr' => ['data-fork' => 'select2'],
        ];
    }
}
