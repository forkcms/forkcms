# Locale / translations

The intention of this article is to explain how the localization in Fork CMS works.

## Introduction

In Fork we have 4 types of locale: act, err, msg and lbl. I will discuss each of them seperatly.

### lbl or labels

Labels are literal translations, in most of the cases, they are a single word or only a few words. For instance "add a group" will be a label. The labels can't contain HTML, and you shouldn't capitalize the first letter of the words (unless appropriate in that language, like uppercase nouns in German): uppercasing the first character when in the beginning of a sentence can be done with modifiers in the templates itself.

### msg or messages

Messages are larger sentences, they can contain HTML-tags.

### err or error messages

Error message are sentences just like messages, though we seperated "regular" messages from errors messages.

### act or actions

Actions are slugs being used in urls, eg: http://www.fork-cms.com/knowledge-base/detail/locale-translations, they can't contain spaces and certain special characters (e.g. the question mark has a special meaning in an url - since it identifies the beginning of the querystring, we shouldn't use it in out slug.)

### How?

Each item in the locale has a type and a name, the name is the unique identifier for the locale. Throughout Fork CMS, we use the CamelCased English value as the name. For errors and messages we use a descriptive CamelCased English string. For instance "groep toevoegen", which is Dutch for "add group" will have the name: AddGroup.

All locale can be managed through the Fork CMS interface. It is available under the tab Settings in the menu Translations.

## Frontend

### Locale in templates

Locale can be used in the template files. If you are working on sites that should be available in multiple languages, this will prove to be handy.

To use it in the template files (.html.twig) you have to use the following syntax:

```
* labels => `{{ 'lbl.NameOfTheTranslation'|trans }}`
* messages => `{{ 'msg.NameOfTheTranslation'|trans }}`
* actions => `{{ 'act.NameOfTheTranslation'|trans }}`
* errors => `{{ 'err.NameOfTheTranslation'|trans }}`
```

So for instance, if you would like to place the word "archive" in the template, you will have to use: `{{ 'lbl.Archive'|trans }}`.

### Locale in code

In the code, there are some methods to retrieve locale. Each type of locale has it own method. They use the Frontend\Core\Engine\Language class.

* `Language::act($key):` used for retrieving actions
* `Language::err($key):` used for retrieving error messages
* `Language::lbl($key):` used for retrieving labels
* `Language::msg($key):` used for retrieving messages

Be aware that you can apply formating (ucfirst, format, ...) yourself.

### Locale in JS-files

Sometimes you will need locale in the JS-files. Therefore the file needs to be parsed through Fork CMS. Usually, when adding a JS-file to a page, you'd go about it like this line of code below:

```
$this->header->addJS('somefile.js');
```

If you need to parse locale in the file, you should instead use:

```
$this->header->addJS('somefile.js', false, true);
```

JS-files with the same name of the action/module will be parsed through Fork CMS by default.

## Backend

The localisation in the backend works the same as in the frontend, with one exception: you can overwrite the default values on a module-basis. So for instance, if you have a label AddGroup and the value is "add group" by default, but you want it to be "add e-mailgroup" in your module, you can overrule it specifically for that module, while leaving the default translation untouched.

Overwriting the values is done by specifying the module when adding the locale. When the locale isn't found in the module, it will fallback to the value of core labels.

### Locale in templates

In the templates, it doesn't change a thing, the correct value will automatically be assigned. Just follow the above-described rules for the frontend.

### Locale in code

In the backend there are some methods to retrieve locale. Each type of locale has it own method. They use the Backend\Core\Engine\Language class.

* `Language::err($key, $module):` used for retrieving errors
* `Language::lbl($key, $module):` used for retrieving labels
* `Language::msg($key, $module):` used for retrieving messages

As you can see, you are able to specify a module. If you don't provide the module-parameter, it will use the current module.
