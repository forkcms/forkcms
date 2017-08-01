UPGRADE FROM 3.7 to 3.8
=======================

## FrameworkBundle

We use the symfony Framework bundle. To make this work, you need a kernel secret in your parameters.yml file. You can add this line to your file:

    kernel.secret:          ThisShouldBeARandomString

Note: insert a random string in there

## Debug component

We don't use the parameters.yml file to set debug mode anymore. This means you can delete the line

    kernel.debug:           true

from your parameters.yml file.

You can now set debug mode by using environment variables. This can be done by adding this to your virtualhost file:

    SetEnv FORK_DEBUG 1

You can also set fork debug mode in your .htaccess file based on a certain url. This can be achieved this way:

    # Set debug mode if the host ends in .dev or in .xip.io
    <IfModule mod_setenvif.c>
        SetEnvIf Host (\.dev|\.xip\.io) FORK_DEBUG=1
    </IfModule>

You should also run this command (or a new composer install) in your console to make sure the assets for the errors are installed the correct way.

    app/console assets:install . && cd tools && ./remove_cache

## Web profiler toolbar

If you want to use Symfony's web profiler toolbar to debug your applications faster, you can do this by setting the environment to dev.
You can do this by setting an environment variable in your virtual host file:

    SetEnv FORK_ENV dev

You can also set dev environment in your .htaccess file based on a certain url. This can be achieved this way:

    # Set debug mode if the host ends in .dev or in .xip.io
    <IfModule mod_setenvif.c>
        SetEnvIf Host (\.dev|\.xip\.io) FORK_ENV=dev
    </IfModule>

## TL;DR version

### Step 1: update parameters.yml

Remove: ````kernel.debug:           true````
Add: ````kernel.secret:          ThisShouldBeARandomString````

### Step 2: install assets for debug component

Run in your project folder: ````app/console assets:install . && cd tools && ./remove_cache````

### Step 3: set debug and environment

In your virtualhost file:

    SetEnv FORK_DEBUG 1
    SetEnv FORK_ENV dev

From your .htaccess file

    # Set debug mode if the host ends in .dev or in .xip.io
    <IfModule mod_setenvif.c>
        SetEnvIf Host (\.dev|\.xip\.io) FORK_DEBUG=1
        SetEnvIf Host (\.dev|\.xip\.io) FORK_ENV=dev
    </IfModule>


For Nginx

    server {
            listen 80; ## listen for ipv4; this line is default and implied
            server_name_in_redirect off;
            server_name fork-cms.com;
            root /var/www/fork;
            index index.php;
            error_log /var/www/fork/var/logs/error-nginx.log;
            access_log /var/www/fork/var/logs/access-nginx.log;
    
        #site root is redirected to the app boot script
        location = / {
            try_files @site @site;
        }
    
        #all other locations try other files first and go to our front controller if none of them exists
        location / {
            try_files $uri $uri/ @site;
        }
    
        location ~ ^/(backend|install|api(\/\d.\d)?(\/client)?).*\.php$ {
            # backend/install/api are existing dirs, but should all pass via the front
            try_files $uri $uri/ @site;
        }
    
        #return 404 for all php files as we do have a front controller
        location ~ \.php$ {
            return 404;
        }
    
        location @site {
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            include fastcgi_params;
            fastcgi_param  FORK_DEBUG 1;
            fastcgi_param  FORK_ENV dev;
            fastcgi_param  SCRIPT_FILENAME $document_root/index.php;
        }
    }

## Twitter cards
* Visit the [Twitter cards validator](https://cards-dev.twitter.com/validator) to whitelist your site.
* Paste your url with the cards meta-tags in the form.
* Fill out the complete form to request a whitelisting approval for your domain.
