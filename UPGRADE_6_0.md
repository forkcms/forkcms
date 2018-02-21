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
* Directory `app` is removed
* Directory `config` is created
* No internal bundles anymore
* Moved root `index.php` to `public`

### Vendor bin files are now in the default folder `vendor/bin`

By removing the following in composer.json
```
"config": {
    "bin-dir": "bin"
},
```
Adding `"App\\": "src",`, and replacing `"./bin/simple-phpunit"` by `"./vendor/bin/simple-phpunit"`.

Replace in `.travis.yml`: `bin/phpcs` by `vendor/bin/phpcs` and `bin/simple-phpunit` by `vendor/bin/simple-phpunit

### Directory `app` is removed

#### ForkController

Before in routing.xml

```
\ForkCMS\App\ForkController
```

After:

```
\App\Controller\ForkController
```

Before:

```php
use ForkCMS\App\ForkController as ...;
```

After:

```php
use App\Controller\ForkController as ...;
```

#### BaseModel

Before:

```
use ForkCMS\App\BaseModel as ...
```

After:

```
use App\Component\Model\BaseModel as ...
```

#### ApplicationInterface

Before:

```
use ForkCMS\App\ApplicationInterface as ...
```

After:

```
use App\Component\Application\ApplicationInterface as ...
```

#### KernelLoader

Before:

```
use ForkCMS\App\KernelLoader as ...
```

After:

```
use App\Component\Application\KernelLoader as ...
```


### No internal bundles anymore

#### Refactor ForkCMSCoreBundle

Before:

```
use ForkCMS\Bundle\CoreBundle\Validator\UrlValidator as ...
```

After:

```
use App\ServiceValidator\UrlValidator as ...
```

Before:

```
use ForkCMS\Bundle\CoreBundle\Tests\Validator\UrlValidatorTest as ...
```

After:

```
use App\Tests\Service\Validator\UrlValidatorTest as ...
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
use App\Console\Installer\CheckRequirementsCommand as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Console\PrepareForReinstallCommand as ...
```

After:

```
use App\Console\Installer\PrepareForReinstallCommand as ...
```

##### Controller

Before:

```
use ForkCMS\Bundle\InstallerBundle\Controller\InstallerController as ...
```

After:

```
use App\Controller\InstallerController as ...
```

##### Entity

Before:

```
use ForkCMS\Bundle\InstallerBundle\Entity as ...
```

After:

```
use App\Controller\InstallerController as ...
```

##### Form Handler

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\DatabaseHandler as ...
```

After:

```
use App\Component\Installer\Handler\DatabaseHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\InstallerHandler as ...
```

After:

```
use App\Component\Installer\Handler\InstallerHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler as ...
```

After:

```
use App\Component\Installer\Handler\LanguagesHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LoginHandler as ...
```

After:

```
use App\Component\Installer\Handler\LoginHandler as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Handler\ModulesHandler as ...
```

After:

```
use App\Component\Installer\Handler\ModulesHandler as ...
```

##### Form Type

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\DatabaseType ...
```

After:

```
use App\Form\Type\Installer\DatabaseType as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\LanguagesType ...
```

After:

```
use App\Form\Type\Installer\LanguagesType as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\LoginType ...
```

After:

```
use App\Form\Type\Installer\LoginType as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Form\Type\ModulesTypes ...
```

After:

```
use App\Form\Type\Installer\ModulesTypes as ...
```

##### Requirement

Before:

```
use ForkCMS\Bundle\InstallerBundle\Requirement\Requirement as ...
```

After:

```
use App\Component\Installer\Requirement\Requirement as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementCategory as ...
```

After:

```
use App\Component\Installer\Requirement\RequirementCategory as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementStatus as ...
```

After:

```
use App\Component\Installer\Requirement\RequirementStatus as ...
```

##### Services

Before:

```
use ForkCMS\Bundle\InstallerBundle\Service\ForkInstaller as ...
```

After:

```
use App\Service\Installer\ForkInstaller as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Service\InstallerConnectionFactory as ...
```

After:

```
use App\Service\Installer\InstallerConnectionFactory as ...
```

Before:

```
use ForkCMS\Bundle\InstallerBundle\Service\RequirementsChecker as ...
```

After:

```
use App\Service\Installer\RequirementsChecker as ...
```
