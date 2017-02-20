# How does the API work

Fork CMS exposes some functionality by default through a RESTful API. The API was built with future expansion in mind, so it is really easy to extend it with new functionality

## How does it work?

The API provides a system to expose functionality to other applications/clients, for instance other applications can communicate with the API to retrieve or even manage data.

We built the system with expandability and the modular structure of Fork CMS in mind. So each module can have its own methods.

### Enabling the API for a user
To be able to communicate with the API, a user should enable this in his/her preferences. Open up Fork CMS, edit your user account (or create a specific API user), click the “rights”-tab and tick the checkbox that states “Enable API-access”.

### The request

A request to the API is done by calling a URL with some parameters. Depending on the action, the parameters should be passed through GET or POST (We don’t officially support PUT and DELETE, because some web-servers don’t).

A typical url for a request would look like:

```
http://<your-fork-url>/api/v1?method=Blog.Comments.GetById&id=1&email=foo@example.com&nonce=1320136792&secret=ed533226259c3ce3382e352634cae0a004efd5b2
```

Lets break this down:

* *http://<your-fork-url>*: The URL to your Fork CMS install
* */api/v1*: This is the API endpoint. The 1.0 is some kind of versioning, so if you ever need to throw around the concept of the API, it won't break existing API implementations.
* *?method=Blog.Comments.GetById*: The method parameter defines which method we will call. In this case we call the commentsGetById-method in the blog module. Each method will be prefaced with the module it lives in.
* *&id=1*: The parameters for the method, these parameters have the same name as the one used in PHP, see below for more information.
* *&email=foo@example.com&nonce=1320136792&secret=ed533226259c3ce3382e352634cae0a004efd5b*: These 3 parameters are used for authenticating.

When a request is made, the system will check several things before calling the method for real. First it will check if the method parameter is available. Then it will check if the module exists and if the API-class is available. When that is checked it will determine if the module can be called.

When all these checks are done, Fork CMS will grab the parameters through the reflection API and will grab the desired datatypes from the PHPDoc, so it is really important you provide correct PHPDoc in your self-defined methods.

The system will then check if all required parameters are provided and if so, the method will be called and the return of the method will be outputted as XML.

### The response

```
<?xml version="1.0" encoding="utf-8"?>
 <fork status_code="200" status="ok" version="3.0.0" endpoint="http://fork-cms.com/api/v1">
     <comments>
         <comment id="1" created_on="2011-10-31T17:40:00+01:00" status="published">
             <article id="1" lang="en">
                 <title>Nunc sediam est</title>
                 <url>http://fork-cms.com/en/blog/detail/nunc-sediam-est</url>
             </article>
             <text>cool!</text>
             <url>http://fork-cms.com/en/blog/detail/nunc-sediam-est#comment-1</url>
             <author email="matthias@spoon-library.com">
                 <name>Matthias Mullie</name>
                 <website>http://www.anantasoft.com</website>
             </author>
         </comment>
     </comments>
 </fork>
```
 
If you provide the format parameter with a value of json (&format=json) the response will be outputted as a JSON-object.

```
{
     "meta": {
         "status_code": 200,
         "status": "ok",
         "version": "3.0.0",
         "endpoint": "http:\/\/fork-cms.com\/api\/v1"
     },
     "data": {
         "comments": [
             {
                 "comment": {
                     "@attributes": {
                         "id": "1",
                         "created_on": "2011-10-31T17:40:00+01:00",
                         "status": "published"
                     },
                     "article": {
                         "@attributes": {
                             "id": "1", "lang": "en"
                         },
                         "title": "Nunc sediam est",
                         "url": "http:\/\/fork-cms.com\/en\/blog\/detail\/nunc-sediam-est"
                     },
                     "text": "cool!",
                     "url": "http:\/\/fork-cms.com\/en\/blog\/detail\/nunc-sediam-est#comment-1",
                     "author": {
                         "@attributes": {
                             "email": "matthias@spoon-library.com"
                         },
                         "name": "Matthias Mullie",
                         "website": "http:\/\/www.mullie.eu"
                     }
                 }
             }
         ]
     }
}
```

As you can see, we provide in each response some basic information about the Fork CMS installation and the status of the call. Let's look into them in detail.

* *status_code*: The status code of the call. This will be 200 if the call went fine, when an error is thrown this won’t be 200, but an error-code, see below.
* *status*: When everything went fine this will be ok, if not this will be error
* *version*: The version of Fork CMS that is used.
* *endpoint*: The endpoint of the API to make the calls to.

### Errors

Sometimes, an error is encountered when calling an API-method. To make it easy for the developers that will communicate with the API of Fork we have a unified error-object. Which look like the one below

```
<?xml version="1.0" encoding="utf-8"?>
<fork status_code="400" status="error" version="2.6.13" endpoint="http://fork-cms.com/api/v1">
    <message>No method parameter provided.</message>
</fork>
```

As you can see the status-attribute contains error, and the status_code isn’t 200. The status-code will be the same as the HTTP-status-code. In the message-tag there will be a textual representation of the error, aka an error-message.

### Authenticating

Some methods will require authorization. The Fork CMS authentication doesn’t allow cleartext-passwords because that would be insecure. Instead, we use the email-address, a nonce and a secret. This may sound complicated, but I will talk you through these steps and all will be clear.

Before you can make authenticated calls we need to grab an API-key. This key will be used to calculate the secret. You can obtain an API-key by calling the core.getApiKey-method with the email-address and the password as GET-parameters.

The URL should look like:

```
http://<your-fork-url>/api/v1?method=Core.GetApiKey&email=<your-email>&password=<your-password>
```

If you open up the URL in your browser you will see an XML-response, which contain a api_key-tag. The value inside that tag is your secret API-key. Store this value in a secure place, because you will use this a lot.

As stated before, an authenticated call will always have three extra parameters:

1. *email*: which is just the email-address of the user
2. *nonce*: this is a random string you will use to calculate the secret, I recommend you to use something that is different for each request, for instance the UNIX-timestamp with a random number appended.
3. *secret*: the secret is a string that is calculated based on the API-key and the nonce.

#### Calculating the secret

The secret is a string we calculate based on something the application and the server both know, and something variable we send with the call. The secret is based on hashing with the sha1 and the md5-algorithm. This way, the the API-key is never used directly, so it's can't be intercepted by malicious attacks.

First we create an md5-hash from the nonce, the random thing we sent along with the request, aka the variable part. Then we concatenate the email-address and the API-key, and build an md5-hash from that combined string. Lastly, we concatenate both strings and hash it through the sha1-algoritme. In a formula it would look like:

```
secret = sha1(md5(nonce) + md5(email + API key))
```

## Implement your own methods

The API isn't definite. We can only provide the basic function, it is up to you to implement your own modules and expose functionality through the API. So we made it fairly easy to add functionality.

### Adding the method

All methods are placed in the backend-module, to respect the modular approach of Fork. To start, we add a file called `api.php` in the engine-folder. All public methods in this file will be available through the API.

As we stated above it is really important to document your methods because we will try to convert the given parameter into the correct datatype.

Let's dissect the `Blog.Comments.Get` method, which you can find under `/src/Backend/Modules/Blog/Engine/Api.php`.

```
 /**
  * Get a single comment
  *
  * @param int $id The id of the comment.
```

As you can see we expect the id-parameter to be an integer, so when it passes through the API-system, it will casted to an integer.

```
  * @return array
  */
 public static function commentsGetById ($id )
```
 
The URL we will request for this method is `http://<your-fork-url>/api/v1?method=Blog.Comments.GetById&id=1&email=foo@example.com&nonce=1320136792&secret=ed533226259c3ce3382e352634cae0a004efd5b2` The parameter id and the parameter in the URL should have the same name, also the parameter is required, so it needs to be present in the request. If it is not present, an error will be thrown.

Another thing that should be noted is that we convert `comments.getById` into `commentsGetById`. All dots after the module are stripped and used as a word-separator for camel-casing the string into the method.

For example: `foo.bar` will call the method bar() in the module foo. `foo.bar.is.a.wierd.method` will call the method `barIsAWierdMethod()` in the foo-module.

```
use Api\V1\Engine\Api as BaseAPI;

...

{
     // authorize
     if (BaseApi::isAuthorized()) {
```

If the necessary authentication parameters are passed and they are valid, the method API:authorize() will return true, so at this point we are sure the user is authenticated. Adding the authorisation to your method only takes this 1 line.

```
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

...

        // get comment
        $comment = (array) BackendBlogModel::getComment($id);
```

At this point, we start building the array that will be returned and used for the output. If you grab the response, which is included below, it will be much clearer.

```
        // init var
        $return = array('comments' => null);

        // any comment found?
        if(empty($comment)) return $return;

        // create array
        $item['comment'] = array();

        // article meta data
        $item['comment']['article']['@attributes']['id'] = $comment['post_id'];
        $item['comment']['article']['@attributes']['lang'] = $comment['language'];
        $item['comment']['article']['title'] = $comment['post_title'];
        $item['comment']['article']['url'] = SITE_URL . BackendModel::getURLForBlock('blog', 'detail', $comment['language']) . '/' . $comment['post_url'];
```

At this point we add a sub-element, which will be converted to a sub-node in the output-XML.

```
        // set attributes
        $item['comment']['@attributes']['id'] = $comment['id'];
        $item['comment']['@attributes']['created_on'] = date('c', $comment['created_on']);
        $item['comment']['@attributes']['status'] = $comment['status'];
```
 
Attributes for a tag should be set in an element called @attributes. For people who have work with SimpleXML this should look familiar.

```
        // set content
        $item['comment']['text'] = $comment['text'];
        $item['comment']['url'] = $item['comment']['article']['url'] . '#comment-' . $comment['id'];
 
        // author data
        $item['comment']['author']['@attributes']['email'] = $comment['email'];
        $item['comment']['author']['name'] = $comment['author'];
        $item['comment']['author']['website'] = $comment['website'];
 
        // add
        $return['comments'][] = $item;
```
 
 
For this method, we chose to embed the comment in an extra comments-tag so the XML is similar to the one that is returned when retrieving multiple comments at once.

```
        return $return;
```

After the array is built, we return it and the API-system will convert it to XML of JSON for us, we don't have to do that manually.

```
    }
}
```

The response will look like this:

```
<?xml version="1.0" encoding="utf-8"?>
 <fork status_code="200" status="ok" version="3.0.0" endpoint="http://fork-cms.com/api/v1">
     <comments>
         <comment id="1" created_on="2011-10-31T17:40:00+01:00" status="published">
             <article id="1" lang="en">
                 <title>Nunc sediam est</title>
                 <url>http://fork-cms.com/en/blog/detail/nunc-sediam-est</url>
             </article>
             <text>cool!</text>
             <url>http://fork-cms.com/en/blog/detail/nunc-sediam-est#comment-1</url>
             <author email="matthias@spoon-library.com">
                 <name>Matthias Mullie</name>
                 <website>http://www.anantasoft.com</website>
             </author>
         </comment>
     </comments>
 </fork>
```

### Implement authentication

Some methods, you don’t want to expose to everyone. Therefore, you can implement authentication. Which only takes one single line of code. On the first line of your method, add BaseApi::authorize();.

The authorize-method will add authentication to your method as described above. This means your clients will need to send the 3 extra parameters. If your client is authenticated, the method will return true.

If you feel the need of implementing your own way of authentication, feel free to do so.

### Handling errors

Not all clients will perform valid calls and you should validate incoming parameters, so you should be able to return error-messages. To return errors we use the same method as the one for returning data, but only with a different status code.

For example, when retrieving the comments you can’t set a limit that is larger then 10000. If you dig into the code, you will see:

```
if($limit > 10000) BaseApi::output(BaseApi::ERROR, array('message' => 'Limit can\'t be
 larger than 10000.'));
```

This is a typical example of throwing an error. As you can see, we used a constant for the status-code. The available options are:

* *BaseApi::OK*: which is 200, and indicates everything went fine.
* *BaseApi::BAD_REQUEST*: which is 400, and means the call isn’t valid.
* *BaseApi::FORBIDDEN*: which is 403 and indicates the client isn’t allowed to do that call.
* *BaseApi::ERROR*: which is 500, and means something went wrong.
* *BaseApi::NOT_FOUND*: which is 404, and indicates something (an item) isn’t found.

As you can see we try to follow the HTTP-status codes.

You don’t have to specify a message on an error, but we recommend to do so. Other application developers like to receive decent feedback.
