<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use Common\ModuleExtraType;
use Common\Uri as CommonUri;
use SpoonDatabase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * The base-class for the installer
 * @deprecated
 */
abstract class ModuleInstaller extends AbstractModuleInstaller
{
}
