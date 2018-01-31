<?php

namespace ForkCMS\Component\Module;

interface CopyModuleToOtherLocaleCommandHandlerInterface
{
    public function handle(CopyModuleToOtherLocaleCommandInterface $command): void;
}
