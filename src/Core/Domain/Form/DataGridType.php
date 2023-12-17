<?php

namespace ForkCMS\Core\Domain\Form;

use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGrid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is a workaround for showing a datagrid in a form.
 */
final class DataGridType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'data_grid';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('data_grid');
        $resolver->setDefaults(
            [
                'data_grid_empty_module_action' => null,
                'data_grid_empty_parameters' => [],
                'data_grid_empty_locale' => null,]
        );
        $resolver->setAllowedTypes('data_grid', DataGrid::class);
        $resolver->setAllowedTypes('data_grid_empty_module_action', [ModuleAction::class, 'null']);
        $resolver->setAllowedTypes('data_grid_empty_parameters', 'array');
        $resolver->setAllowedTypes('data_grid_empty_locale', [Locale::class, 'null']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['data_grid'] = $options['data_grid'];
        if ($options['data_grid_empty_module_action'] instanceof ModuleAction) {
            $view->vars['data_grid_empty_action'] = $options['data_grid_empty_module_action']->getAction()->getName();
            $view->vars['data_grid_empty_module'] = $options['data_grid_empty_module_action']->getModule()->getName();
        }
        $view->vars['data_grid_empty_parameters'] = $options['data_grid_empty_parameters'];
        $view->vars['data_grid_empty_locale'] = $options['data_grid_empty_locale'];
    }
}
