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


### No internal bundles anymore

#### Refactor CoreBundle

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

