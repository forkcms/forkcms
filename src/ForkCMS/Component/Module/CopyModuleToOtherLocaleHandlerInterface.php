<?php

namespace ForkCMS\Component\Module;

interface CopyModuleToOtherLocaleHandlerInterface
{
    public function handle(CopyModuleToOtherLocaleInterface $command): void;
}
