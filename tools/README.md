# Tools
In the tools folder you will find scripts, stuff that can come in handy.
There are some scripts that enable you to perform tasks that recure on a regular base. For now these scripts are only available on *NIX-systems. In the future they may be ported to Windows.

## Install
Their is no real installation of the scripts the only thing you should do is make them executable, this can be done with the following command. Make sure to replace `<script_name>` with the name of the script.

	chmod +x <script_name>

## Executing the scripts
After making the scripts executable you can execute them with the following command. Make sure to replace `<script_name>` with the name of the script. **The scripts should be executed from this folder**

	./<script_name>
	
For example

	./minify

## Scripts

### check_code
The check_code-script will run PHP CodeSniffer with all settings that are needed. It will check your code against the styleguide, will detect wierd/faulty code, ...

It can be executed with the following command

	./check_code

### minify
The minify-script will minify the CSS and JS used by the backend. These files will be used by the backend when not in DEBUG-mode.
**This script should be executed before putting a release in the wild.**

It can be executed with the following command

	./minify

### prepare_for_reinstall
This script is more an internal tool, it removes all files so the Fork is just like it wasn't installed before.

It can be executed with the following command

	./prepare_for_reinstall

### remove_cache
The remove_cache-script will clear folders that contain cached files. And can be executed with the following command:

	./remove_cache

	