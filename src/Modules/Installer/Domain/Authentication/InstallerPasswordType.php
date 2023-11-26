<?php

namespace ForkCMS\Modules\Installer\Domain\Authentication;

use ForkCMS\Core\Domain\Form\TogglePasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This prevents previously submitted passwords from being cleared.
 */
final class InstallerPasswordType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$options['always_empty'] && !$form->isSubmitted()) {
            $view->vars['value'] = $form->getViewData();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('always_empty', false);
    }

    public function getParent(): string
    {
        return TogglePasswordType::class;
    }
}
