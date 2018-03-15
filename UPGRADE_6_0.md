UPGRADE FROM 5.x to 6.0
=======================

## Symfony 4.0

We follow the changed directory structure from Symfony 4.0.
```
fork-cms/
├─ assets/
├─ bin/
│  └─ console
├─ config/
├─ public/
│  └─ index.php
├─ src/
│  └─ ...
├─ templates/
├─ tests/
├─ translations/
├─ var/
│  ├─ cache/
│  ├─ log/
│  └─ ...
└─ vendor/
```

Which means:
* One `ForkCMS` namespace for `Backend`/`Frontend`/`Common` and Tests
* Using `var/log` instead of `var/logs`
* Removed CKFinder
* Directory `app` is removed
* Directory `config` is created
* No internal bundles anymore
* Moved root `index.php` to `public`

### One `App` namespace

To make everything work for Symfony 4 we had to:
* Replace `Backend` by `ForkCMS\Backend`
* Replace `Common` by `ForkCMS\Common`
* Replace `Frontend` by `ForkCMS\Frontend`
* Moved files from `ForkCMS\App` to `App` namespace
* Moved all tests from backend/common/frontend to `ForkCMS\Tests`

We now have a clean composer.json autoload
```
    "autoload": {
        "psr-4": {
            "ForkCMS\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "ForkCMS\\Tests\\": "tests/"
        }
    },
```

### Refactor files from `ForkCMS\App` into `ForkCMS` namespace

#### ForkController

Before in routing.xml

```
ForkCMS\App\ForkController
```

After:

```
ForkCMS\Controller\ForkController
```

Before:

```php
use ForkCMS\App\ForkController as ...;
```

After:

```php
use ForkCMS\Controller\ForkController as ...;
```

#### BaseModel

Before:

```
use ForkCMS\App\BaseModel as ...
```

After:

```
use ForkCMS\Component\Model\BaseModel as ...
```

#### ApplicationInterface

Before:

```
use ForkCMS\App\ApplicationInterface as ...
```

After:

```
use ForkCMS\Component\Application\ApplicationInterface as ...
```

#### KernelLoader

Before:

```
use ForkCMS\App\KernelLoader as ...
```

After:

```
use ForkCMS\Component\Application\KernelLoader as ...
```


### Using `var/log` instead of `var/logs`

### Removed CKFinder

Using it in combination with the new Kernel was impossible.
And because it was already deprecated and not being used anymore in Fork CMS.
We removed it.

### Vendor bin files are now in the default folder `vendor/bin`

By removing the following in composer.json
```
"config": {
    "bin-dir": "bin"
},
```
Adding `"ForkCMS\\": "src",`, and replacing `"./bin/simple-phpunit"` by `"./vendor/bin/simple-phpunit"`.

Replace in `.travis.yml`: `bin/phpcs` by `vendor/bin/phpcs` and `bin/simple-phpunit` by `vendor/bin/simple-phpunit

### No internal bundles anymore

#### Refactor ForkCMSCoreBundle

Before:

```
use ForkCMS\Bundle\CoreBundle\Validator\UrlValidator as ...
```

After:

```
use ForkCMS\Utility\Validator\UrlValidator as ...
```

Before:

```
use ForkCMS\Bundle\CoreBundle\Tests\Validator\UrlValidatorTest as ...
```

After:

```
use ForkCMS\Tests\Service\Validator\UrlValidatorTest as ...
```

#### Refactor ForkCMSInstallerBundle

Moved `src/ForkCMS/Bundle/InstallerBundle/Resources/views` to `templates/installer`
and `src/ForkCMS/Bundle/InstallerBundle/Resources/public` to `public/installer`.


##### Console

Before:

```
use ForkCMS\Bundle\InstallerBundle\Console\CheckRequirementsCommand as ...
```

After:

```
use ForkCMS\Console\Installer\CheckRequirementsCommand as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Console\PrepareForReinstallCommand as ...
```

After:

```
use ForkCMS\Console\Installer\PrepareForReinstallCommand as ...
```

##### Controller

Before:

```
use ForkCMS\Bundle\InstallerBundle\Controller\InstallerController as ...
```

After:

```
use ForkCMS\Controller\InstallerController as ...
```

##### Entity

Before:

```
use ForkCMS\Bundle\InstallerBundle\Entity as ...
```

After:

```
use ForkCMS\Controller\InstallerController as ...
```

##### Form Handler

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\DatabaseHandler as ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Handler\DatabaseHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\InstallerHandler as ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Handler\InstallerHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler as ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Handler\LanguagesHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LoginHandler as ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Handler\LoginHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\ModulesHandler as ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Handler\ModulesHandler as ...
```

##### Form Type

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\DatabaseType ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Type\DatabaseType as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\LanguagesType ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Type\LanguagesType as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\LoginType ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Type\LoginType as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\ModulesTypes ...
```

After:

```
use ForkCMS\Utility\Installer\Form\Type\ModulesTypes as ...
```

##### Requirement

Before:

```
use ForkCMS\Bundle\InstallerBundle\Requirement\Requirement as ...
```

After:

```
use ForkCMS\Utility\Installer\Requirement\Requirement as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementCategory as ...
```

After:

```
use ForkCMS\Utility\Installer\Requirement\RequirementCategory as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementStatus as ...
```

After:

```
use ForkCMS\Utility\Installer\Requirement\RequirementStatus as ...
```

##### Services

Before:

```
use ForkCMS\Bundle\InstallerBundle\Service\ForkInstaller as ...
```

After:

```
use ForkCMS\Utility\Installer\ForkInstaller as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Service\InstallerConnectionFactory as ...
```

After:

```
use ForkCMS\Utility\Installer\InstallerConnectionFactory as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Service\RequirementsChecker as ...
```

After:

```
use ForkCMS\Utility\Installer\RequirementsChecker as ...
```

## Move + rename database_data_collector.html.twig

Move and rename `app/Resources/views/database_data_collector.html.twig`
to `templates/bundles/WebProfilerBundle/Profiler/spoon_database_data_collec
tor.html.twig`
