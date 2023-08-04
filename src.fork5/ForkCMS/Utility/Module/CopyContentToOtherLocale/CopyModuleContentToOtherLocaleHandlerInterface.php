<?php

namespace ForkCMS\Utility\Module\CopyContentToOtherLocale;

interface CopyModuleContentToOtherLocaleHandlerInterface
{
    public function handle(CopyModuleContentToOtherLocaleInterface $command): void;
}
