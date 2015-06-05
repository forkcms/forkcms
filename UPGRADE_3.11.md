## SpoonHttp is deprecated

As you all know Spoon isn't supported anymore, therefore we are trying to 
replace all functionality that was provided by Spoon with SYmfony-functionality
or native code. 

With this upgrade we have replace all functionality provided with SpoonHttp. 
There are several methods, each one is discussed below.

### SpoonHttp::getContent()

Before:

```php
SpoonHttp::getContent($url)
```

After:

```php
file_get_content($url)
```

> Remark: fopen wrapper should be enabled.
