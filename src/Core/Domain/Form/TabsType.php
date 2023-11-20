<?php

namespace ForkCMS\Core\Domain\Form;

use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

final class TabsType extends AbstractType
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['tabs'] as $label => $fields) {
            $builder->add(
                self::getTabNameForLabel($label, $this->slugger),
                TabType::class,
                [
                    'fields' => $fields,
                    'label' => $label,
                    'inherit_data' => $options['tab_inherit_data'],
                    'attr' => $options['tab_attr'],
                ]
            );
        }
    }

    /**
     * @param ?SluggerInterface $slugger You don't need to provide the slugger if you are trying to get an existing tab
     */
    public static function getTabNameForLabel(string $label, ?SluggerInterface $slugger = null): string
    {
        static $cachedSlugger = null;
        if ($cachedSlugger === null) {
            $cachedSlugger = $slugger ?? throw new RuntimeException('No slugger provided');
        }

        return $cachedSlugger->slug(str_replace('lbl.', 'tab.', $label), '_');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(
                [
                    'inherit_data' => true,
                    'tab_inherit_data' => true,
                    'options' => [],
                    'tabs' => [],
                    'label' => false,
                    'tab_attr' => [],
                    'left_tabs_count' => null,
                ]
            )
            ->addAllowedTypes('tabs', 'array');
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['left_tabs_count'] = $options['left_tabs_count'];
    }
}
