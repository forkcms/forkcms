# Publish your website

The **Debug mode** should be disabled on every active website, otherwise Google won't index and no caching would be used. Switching between modes can be done in the parameters, which are stored in a yaml-file that is located in /app/config/.

If you want to publish the website on a different server then during the installation, make sure the **database** properties are configured correctly. These can be changed in the same parameters file as the debug mode. Also, make sure the **webserver** is configured correctly including the required php configuration (Extensions, functions and settings). At last check if following **directories** has writable rights.

* /src/Backend/Cache/*
* /src/Backend/Modules/
* /src/Frontend/Cache/*
* /src/Frontend/Files/*
* /src/Frontend/Modules/
* /src/Frontend/Themes/

It is recommended to delete - or not to upload - the **install- and tools folder**.
