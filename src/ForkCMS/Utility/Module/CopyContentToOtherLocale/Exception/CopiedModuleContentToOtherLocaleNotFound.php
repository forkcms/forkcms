<?php

namespace ForkCMS\Utility\Module\CopyContentToOtherLocale\Exception;

class CopiedModuleContentToOtherLocaleNotFound extends \Exception
{
    public static function forId(string $moduleName, int $id): self
    {
        return new self('The id "' . $id . '" does not exist in the map for the module "' . $moduleName. '".');
    }

    public static function forModule(string $moduleName): self
    {
        return new self(
            'The module "' . $moduleName . '" has not yet been copied or is not installed.
             You should increase the priority, if you want it to be executed before this handler.
             Then you can access eventual ids.'
        );
    }
}
