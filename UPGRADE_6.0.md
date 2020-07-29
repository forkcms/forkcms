UPGRADE FROM 5.x to 6.0
=======================

## Introdcure ES6

### Changes in Frontend

#### Social media plugin

In the past there was a ‘shareMenu’ function, which you could use to render share buttons for different social media platforms. We removed this functionality, as it was not maintained anymore. More importantly it was mostly against the terms of different social media platforms. Therefore we recommend you to use the official share-widgets.

### Changes in backend

#### Removal of keyValueBox, tagsBox, multipleSelectbox 

There were some functions (keyValueBox, tagBox, multipleSelectbox) that are no longer used in the core and no longer maintained.
Instead of multipleSelectBox you can now use the [Select2](https://select2.org/) plugin, that is configured by default in Fork.
For the tagsBox there is already [Bootstrap tagsinput](https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/) configured.

#### urlencode and urldecode are removed

The methods Url.encode & Url.decode are removed. These functions are available in native JavaScript, so use: encodeURI() and decodeURI() if needed.

#### CKEditor js & css removed

With the new editor there is no need for the js and css related to CKEditor. If you still want to use CKEditor, which we don’t recommend, you should add the needed files yourself.

#### form utils are removed

The form util functions are removed as in most cases a global function is available. In some modules the form utils were not used, which made it pointless to keep maintaining these utility functions.

Example: There was a static function that checks if a checkbox is checked. This function was never used. If there is a need to check the value of a checkbox, this happens inline.

#### Theme is defined in .env

The frontend theme is set in the `.env` file. 
This is necessary for webpack to know which theme to compile. In our Symfony config we also need the theme to set the build paths of our multiple entries.

#### Double js methods

We no longer use expliciet double js methods. Some utils (examples: array, string, url) are available and used in the backend. These are also available in the frontend.

#### No more build assets in Git

Files that are generated are no longer stored in Git. This means you won’t be able to download Fork as a zip file and upload it to your hosting provider and start.
We think the developer community has matured enough that we can expect them to know how to build the assets beforehand.

To generate the needed assets you can run:
```node
npm install
```

```
npm run build
```
	
If you are developing locally you should run:
```
npm install
```

Watch frontend core: `npm run dev:frontend`

Watch frontend theme `npm run dev:frontend:theme`

Watch backend core: `npm run dev:backend`

Watch installer: `npm run dev:installer`
