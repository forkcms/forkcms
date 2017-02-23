# Passing data from PHP to javascript

In the 3.4-release we introduced some nifty new features: `jsFrontend.locale.*`, `jsFrontend.data.*`. These objects were introduced to handle passing variable data from PHP into JS.

## Some history

Before the 3.4-release there were 2 major problems with JS-files. To be able to localise them you needed to parse them through PHP, which ment they couldn't be cached, due to the variable data of the languages. The second problem was you couldn't pass custom data to the files, we solved this by parsing some inline-script-tags in the template, this was a reasonable solution, but it ment there was the possibility of a lot of inline-script tags. Also we had to add extra checks in the JS-files to check if the data was available.

## Language

With the 3.4-release you are able to use the `jsFrontend.locale.*` methods.

There is a method for each type of locale:

* `jsFrontend.locale.act`, for retrieving action.
* `jsFrontend.locale.err`, for retrieving errors.
* `jsFrontend.locale.lbl`, for retrieving labels.
* `jsFrontend.locale.msg`, for retrieving messages

So instead of using `{$lblSomeLabel}` use `jsFrontend.locale.lbl('SomeLabel');`

### How does it work?

Well, when you alter the locale the locale is stored as a PHP-array, and in JSON-format, when you use one of the methods above, we will check if the JSON is already loaded, if not we will load it and cache it in memory. This means that the locale is only loaded if needed, so no extra overhead if you don't use locale in your JS-files.

If the locale data is loaded we can retrieve the correct value. If it doesn't exists we return a string `{<type><key>}`.

## Custom data

Passing data to JS from within PHP can be tricky, there are several pitfalls. For instance you have to make sure the data is available when the jsFiles are loaded. Therefore we added a proper way:

From within PHP you can use the method `$this->header->addJsData(<module>, <key>, <value>)`, so basically it is a key value-store, you give it a unique name and we will take care of it.

In JS you can grab the data by using the `jsFrontend.data.get(<module>.<key>)`-method. As you can see each key will be prefixed with the module wherin the action or widget lives, we added this to handle scope-problems.

In several occasions it will be necesary to check if the data is available, this can be achieved with the `jsFrontend.data.exists(<module>.<key>)`-method.

The data you passed through PHP is handled as JSON, so you should be able to have keys like: `location.settings.zoom.level`, therefore you will need to add it in PHP like:

```
$this->header->addJsData($this->getModule(), 'settings', array('zoom' => array('level' => 12)));
```

In JS you can retrieve this value by using:

```
jsFrontend.data.get('location.settings.zoom.level');
```

### How does it work?

In the head we add a JS-variable with alle the passed data in a JSON-object. When you use the `jsFrontend.data.*` methods we will check if the items exists and return it.
