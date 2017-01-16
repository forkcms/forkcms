UPGRADE FROM 4.x to 5.0
=======================

## API is removed

The full API is removed. If you need to expose an API for your project you should look into:

* [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)

You should remove all usages of:

* `API_CORE_PATH`-constant
* `fork_api_private_key`-setting
* `fork_api_public_key`-setting
* `BackendModel::ping`-method
* `ping_services`-setting from the Blog-module
* `ForkAPI`-class
* `api_access`-usersetting
* `ApiTestCase`-class
* `FrontendModel::pushToAppleApp`-method


## triggerEvent

...

## Backend\Core\Engine\Language moved to its namespace

Before:

```php
use Backend\Core\Engine\Language as ...;
```

After:

```php
use Backend\Core\Language\Language as ...;
```


## Frontend\Core\Engine\Language moved to its namespace 

Before:

```php
use Frontend\Core\Engine\Language as ...;
```

After:

```php
use Frontend\Core\Language\Language as ...;
```
