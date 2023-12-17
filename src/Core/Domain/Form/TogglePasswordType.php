<?php

namespace ForkCMS\Core\Domain\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class TogglePasswordType extends AbstractType
{
    public function getParent(): string
    {
        return PasswordType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'toggle_password';
    }
}
