<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

interface PageDataTypeInterface
{
    public static function transform(?array $persistedData, array $transformedData): array;

    public static function reverseTransform(array $submittedData, array $transformedData): array;
}
