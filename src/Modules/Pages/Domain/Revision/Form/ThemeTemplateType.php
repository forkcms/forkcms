<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Form;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ThemeTemplateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'label' => 'lbl.Template',
                'attr' => [
                    'class' => 'row',
                    'data-role' => 'template-switcher',
                    'autocomplete' => 'off',
                ],
                'class' => ThemeTemplate::class,
                'query_builder' => static function (ThemeTemplateRepository $themeTemplateRepository): QueryBuilder {
                    return $themeTemplateRepository
                        ->createQueryBuilder('tt')
                        ->innerJoin('tt.theme', 't', Join::WITH, 't.active = 1 AND tt.active = 1');
                },
                'choice_label' => static fn (ThemeTemplate $themeTemplate): string => $themeTemplate->getName(),
                'choice_attr' => static fn (ThemeTemplate $themeTemplate): array => [
                    'data-template' => $themeTemplate
                ],
                'expanded' => true,
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
