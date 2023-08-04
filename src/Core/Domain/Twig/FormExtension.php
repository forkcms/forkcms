<?php

namespace ForkCMS\Core\Domain\Twig;

use ForkCMS\Modules\Backend\Domain\Action\ActionName;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FormExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'locateFormView',
                [$this, 'locateFormView'],
            ),
        ];
    }

    public function locateFormView(FormView $formView, string $name): ?FormView
    {
        while ($formView->parent) {
            $formView = $formView->parent;
        }

        return $this->findFormViewRecursively($formView, $name);
    }

    private function findFormViewRecursively(FormView $formView, string $name): ?FormView
    {
        if ($formView->vars['name'] === $name) {
            return $formView;
        }

        foreach ($formView->children as $child) {
            $result = $this->findFormViewRecursively($child, $name);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }
}
