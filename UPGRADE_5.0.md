UPGRADE FROM 4.x to 5.0
=======================

## API is removed

...

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

