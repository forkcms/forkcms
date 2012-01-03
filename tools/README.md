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

### batch_resize
The batch_resize-script will resize the images in the provided folder. You can specify a height and/or width.

<small>Remark: The original files will be modified</small>

It can be executed with the following command

	./batch-resize -w 75 -h 75 ./
	
	

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


### stats
The stats-script will run severals scripts (PHP Code Sniffer, PHP Mess Detection, PHP Depend an PHP Loc). Each of this script will generate an XML file (in the `reports`-folder) that contains useful numbers.

#### PHP Code Sniffer (phpcs.xml)
Will contain the same as running `check_code`. It will the code against the styleguide, it will detect deprecated stuff, ... 

Most warnings will be about CyclomaticComplexity or NestingLevel, you can't ignore them, but some of the reported methods are complicated for a reason.

#### PHP Mess Detection (phpmd.xml)
Basicaly PHP Mess Detection is a spin-off of PHP Depend (see below) it will also check the code for possible bugs, suboptimal code, ... Once again it is important to intepret the result, not everything that is reported means that the code is bad.

<small>Remark: it seems like PHP Mess Detection ignores the --ignore parameter, so all editor-crap-code is included, you can ignore all the errors about files inside the ckeditor and ckfinder-folder</small>

#### PHP Depend (pdepend.xml, pdepend_chart.svg, pdepend_pyramid.svg)
PHP Depend is a tool that performs code analysis. It calculates the software metrics, each number represents a aspect of the code.

below you can find a list, so you can interpret the results

* ahh:		Average Hierarchy Height	The average of the maximum lenght from a root class to ist deepest subclass subclass
* andc:		Average Number of Derived Classes	The average of direct subclasses of a class
* calls:	Number of Method or Function Calls
* ccn:		Cyclomatic Complexity Number
* ccn2:		Extended Cyclomatic Complexity Number
* cis:		Class Interface Size	CIS = public(NOM + VARS) Measures the size of the interface from other parts of the system to a class.
* cloc:		Comment Lines fo Code
* clsa:		Number of Abstract Classes
* clsc:		Number of Concrete Classes
* cr:		Code Rank Google PageRank applied on Packages and Classes. Classes with a high value should be tested frequently.
* csz:		Class Size Number 	CSZ = NOM + VARS Measures the size of a class concerning operations and data.
* dit:		Depth of Inheritance Tree Depth of inheritance to root class
* eloc:		Executable Lines of Code
* fanout:	Number of Fanouts
* leafs:	Number of Leaf Classes
* lloc:		Logical Lines Of Code
* loc:		Lines Of Code
* maxDIT:	Max Depth of Inheritance Tree. Maximum depth of inheritance
* noam:		Number Of Added Methods
* nocc:		Number Of Child Classes
* noom:		Number Of Overwritten Methods
* ncloc:	Non Comment Lines Of Code
* noc:		Number Of Classes
* nof:		Number Of Functions
* noi:		Number Of Interfaces
* nom:		Number Of Methods
* nop:		Number of Packages
* npath:	NPath Complexity
* rcr:		Reverse Code Rank
* roots:	Number of Root Classes
* vars:		Properties
* varsi:	Inherited Properties
* varsnp:	Non Private Properties
* wmc:		Weighted Method Count. The WMC metric is the sum of the complexities of all declared methods and constructors of class.
* wmci:		Inherited Weighted Method Count. Same as wmc, but only inherited methods.
* wmcnp:	Non Private Weighted Method Count. Same as wmc, but only non private methods.

For more information on PHP Depend reports, see: http://pdepend.org/documentation/handbook/reports.html

#### PHP Loc
Your for the sake of statistics, some raw numbers
