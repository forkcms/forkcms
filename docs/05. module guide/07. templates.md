# Templates

## Twig

We use twig as our template engine. An introduction about twig can be found in [Twig for Template Designers](http://twig.sensiolabs.org/doc/templates.html)

If you want more info, you can visit the [full documentation](http://twig.sensiolabs.org/documentation)

## Locale

When you're developing a module, you'd rather not translate your content directly in your template files. Instead you use template tags and, if a different locale is selected, these tags will output the correct translation. You see a couple of locale in our example:

* `{{ 'msg.WrittenBy'|trans }}`
* `{{ 'lbl.On'|trans }}`
* `{{ 'lbl.PeopleThinkThisPostIsAwesome'|trans }}`

Everything that applies to normal data applies to labels as well. Each label is surrounded by curly braces, preceded by a dollar sign, and can be used in combination with modifiers (see next chapter). There are, however, two differences that set labels apart:

The first is that you don't have to parse them in the template yourself. This happens automatically.

The second is that labels always begin with lbl, msg, act, or err. You can read more about this in the next chapter.
