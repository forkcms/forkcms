# Applications & Library

## Applications

Fork CMS exists out of three applications. The first, perhaps the most important one, is the backend. This is where the content of the website will be created or modified.

## Backend

From a user's perspective, this part of a Fork CMS website can be found at http://myforksite.dev/private. It's here where you login with the username and password you entered during the installation.
In the filesystem, everything concerning the backend is located in the /backend folder.

## Frontend

The frontend is where the actual website is located. From a user's perspective the location is of course http://myforksite.dev. The files for the webdeveloper however are located in /frontend folder.

## API

A third application, often not used, is the API or Application Programming Interface. With this application you can make your website/application accessible by external websites, applications, ... We'll discuss this in Chapter 22: API.

## Library

All three Applications still make extensive use of the Spoon Library. If you've never worked with Spoon it might be a good idea to browse through the [Spoon-documentation](http://www.spoon-library.com), although the Spoon code we'll discuss is pretty self explanatory.

We're dropping Spoon in favor of [Symfony](http://symfony.com). The progress can be followed on [GitHub](https://github.com/forkcms/) or easier to read version on our [blog](http://fork-cms.com/blog).

You'll find Spoon in the vendor Folder. In this folder, all external libraries got imported by [Composer](http://getcomposer.org/).

If you're using other library's, f.e. to link your website with facebook, twitter, picasa,... it might be a good idea to save them in the library/external folder.