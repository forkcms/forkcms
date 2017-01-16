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
* `BackendModel::ping()`-method
* `ping_services`-setting from the Blog-module
* `ForkAPI`-class
* `api_access`-usersetting
* `ApiTestCase`-class
* `FrontendModel::pushToAppleApp()`-method


## triggerEvent

Our own implementation of events has been removed. If you need to implement the
same behaviour you should look into [Symfony's EventDispatcher Component](http://symfony.com/doc/current/components/event_dispatcher.html).

You should remove all usages of:

* `Backend\Core\Installer\ModuleInstaller->subscribeToEvent()`
* `BackendModel::triggerEvent()`
* `Common\Core\Model::subscribeToEvent()`
* `Common\Core\Model::triggerEvent()`
* `Common\Core\Model::startProcessingHooks()`
* `Common\Core\Model::unsubscribeFromEvent()`
* `FrontendModel::triggerEvent()`


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

## invalidateFrontendCache is removed

Since we moved to Twig, and Twig does not use the same caching mechanisme. The ``-method is removed.
If you need template caching you can take a look at [Twig cache extension](https://github.com/asm89/twig-cache-extension).

You should remove all usages of:

* `Backend\Core\Engine\Model::invalidateFrontendCache()`


## `getGroups` in favor of `getGroupId`

If your code uses `getGroupId` you should rewrite it to use `getGroups` instead.
