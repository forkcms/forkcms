UPGRADE FROM 5.x to X.0
=======================

We restructured everything, for simplicity, for symfony 4 and for future benefit.

# Changed classes

## Backend\Core\Language\Language moved to its namespace

Before:

```php
use Backend\Core\Language\Language;
```

After:

```php
use App\Component\Locale\BackendLanguage;
```
> Also replace every `BL::` to `BackendLanguage::

## Backend\Core\Language\Language moved to its namespace

Before:

```php
use Frontend\Core\Language\Language;
```

After:

```php
use App\Component\Locale\FrontendLanguage;
```
> Also replace every `FL::` to `FrontendLanguage::