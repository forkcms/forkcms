# API

When you want to link your website to the outside world, it might be a good idea to provide an API to this outside world. By using the API, other programmers connect there applications, iPhone Apps, websites, ... with your website, or perhaps you could write your own iPhone App (and sell it for just € 9,99 in the AppStore) which allows to post blog posts to your website.
We'll use an other, less complicated example to illustrate the principles of an API.

## Engine

First you create a file named "api.php" in the /src/Backend/Modules/ModuleName/Engine folder. In this file you define a class called `Api`.

Then, for every action external applications should be able to perform you define a public static method.

In our case we'll just have one method, `showTopAwesome`. This method will return the most (number) awesome posts of the last (days) days.

```
namespace Backend\Modules\MiniBlog\Engine;

use Api\V1\Engine\Api as BaseAPI;
use Backend\Modules\MiniBlog\Engine\Model as BackendMiniBlogModel;

class Api
{
    public static function showTopAwesome()
    {
        $number = (int)\SpoonFilter::getGetValue('number', null, 10);
        $days = (int)\SpoonFilter::getGetValue('days', null, 7);
        BaseAPI::output('200', BackendMiniBlogModel::getTopAwesome($number, $days));
    }
}
```

As you can see we expect the data to be GET-variables but if you feel like working with POST-variables,... your choice.

## Return

When you want to use the method above, you need to call the following url:
  /api/v1/?method=MiniBlog.showTopAwesome&format=json&number=10&days=7

The first parameter is the name of the module and the name of the action separated with a dot. This action is the real method name, not the "reversed" camelCased name as it's used with class- and filenames.

When you're using the output function, you need to supply the format parameter, which has to be equal to "json" or "xml". The data we fetched and passed on the output method will be converted to one of these two formats.

The last two parameters are "number" and "days". They are used only in this action. Should they be omitted, then the default value, given as the third parameter of the getGetValue-method, is used.

> **Don't return**
> In our example the API is supposed to return some data, but of course you can create method's that will not be returning any data (f.e. posting a new blog post). In that case, just don't call the output method.
> Although you could provide some feedback wether to post was successful.
