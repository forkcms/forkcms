<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The uninstaller interface
 */
interface UninstallerInterface
{
    public function uninstall(): void;

    public function getInput(): ?InputInterface;

    public function getOutput(): ?OutputInterface;
}
