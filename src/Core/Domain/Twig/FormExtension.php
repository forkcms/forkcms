<?php

namespace ForkCMS\Core\Domain\Twig;

use ForkCMS\Core\Domain\Form\EditorType;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class FormExtension extends AbstractExtension
{
    public function __construct(private readonly EditorType $editorType)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locateFormView', [$this, 'locateFormView']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'parseEditor',
                [$this->editorType, 'parseContent'],
                [
                    'needs_environment' => false,
                    'needs_context' => false,
                    'is_safe' => ['all'],
                ]
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
