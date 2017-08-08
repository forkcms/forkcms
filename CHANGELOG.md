5.0.1 (2017-08-07)
------------------

Hotfix number one is a fact!

Bugfixes:

* Installer: Fixes installer when you want mailmotor + example data [#2165](https://github.com/forkcms/forkcms/pull/2165)
* Installer: Install script fix [#2169](https://github.com/forkcms/forkcms/pull/2169)

5.0.0 (2017-08-04)
------------------

Since this is a major update we also provided an [upgrade guide](https://github.com/forkcms/forkcms/blob/master/UPGRADE_5.0.md)

Some pull requests fixed bugs that were introduced during the development of fork 5 and won't be listed here

Bugfixes:

* Analytics: Removed extra whitespace in template [#2160](https://github.com/forkcms/forkcms/pull/2160)
* Analytics: Fixed typos and improved explanation in the translatiosn [#2159](https://github.com/forkcms/forkcms/pull/2159)
* Extensions: Add missing translations [#2100](https://github.com/forkcms/forkcms/pull/2100)
* FormBuilder: Fix encoded htmlspecial chars in the email [#2017](https://github.com/forkcms/forkcms/pull/2017)

…

Features:

* Core: Bump minimal php version [#1923](https://github.com/forkcms/forkcms/pull/1923)
* Core: Remove deprecated code [#1941](https://github.com/forkcms/forkcms/pull/1941)
* Core: Add macro for datagrids [#1969](https://github.com/forkcms/forkcms/pull/1969)
* Core: Add a cache clearing button to the backend [#1993](https://github.com/forkcms/forkcms/pull/1993)
* Core: Added console command to generate the thumbnails [#1988](https://github.com/forkcms/forkcms/pull/1988)
* Core: Added a datepicker [#2112](https://github.com/forkcms/forkcms/pull/2112)
* Core: Added docker config [#2038](https://github.com/forkcms/forkcms/pull/2038)
* Core: Cookies now need to be set with the `fork.cookie` service
* Triton: After many years of service the triton theme has been retired and replaced with a bootstrap theme [#1930](https://github.com/forkcms/forkcms/pull/1930)
* FormBuilder: Added possibility to send a confirmation mail from the form builder [#1602](https://github.com/forkcms/forkcms/pull/1602)
* FormBuilder: Add reCAPTCHA field to formbuilder [#2008](https://github.com/forkcms/forkcms/pull/2008)
* MediaLibrary: A new core module to help you manage media across modules [#1986](https://github.com/forkcms/forkcms/pull/1986)
* MediaGalleries: A new core module to help you create sliders etc from media in the media library [#1986](https://github.com/forkcms/forkcms/pull/1986)
* Pages: Adding content in a preformatted way to a page has been made easier with the addition of user templates [#1958](https://github.com/forkcms/forkcms/pull/1958)

…

Improvements:

* Core: Moved scripts into console commands [#1942](https://github.com/forkcms/forkcms/pull/1942)
* Core: Enable the cookie bar by default when the timezone is in europe [#1957](https://github.com/forkcms/forkcms/pull/1957)
* Core: Improve the breadcrumb in the backend [#1968](https://github.com/forkcms/forkcms/pull/1968)
* Core: Move from PSR-0 to PSR-4 [#1975](https://github.com/forkcms/forkcms/pull/1975)
* Core: Improve the https htaccess entries [#1979](https://github.com/forkcms/forkcms/pull/1979)
* Core: Change meta table to InnoDB [#1980](https://github.com/forkcms/forkcms/pull/1980)
* Core: Move the docs from a separate repo to the docs directory [#1985](https://github.com/forkcms/forkcms/pull/1985)
* Core: Style form elements in fork style by default [#1967](https://github.com/forkcms/forkcms/pull/1967)
* Core: Fixed wrong documentation about installation zip [#2007](https://github.com/forkcms/forkcms/pull/2007)
* Core: Added php 7.1 typehints and cleaned up some legacy code [#2001](https://github.com/forkcms/forkcms/pull/2001)
* Core: Replace \&amp; by & as parameter query divider [#2095](https://github.com/forkcms/forkcms/pull/2095)
* Core: Use post requests instead of get requests to delete items [#2090](https://github.com/forkcms/forkcms/pull/2090)
* Core: Use symfony http code constants [#2123](https://github.com/forkcms/forkcms/pull/2123)
* Core: Added test command to composer [#2141](https://github.com/forkcms/forkcms/pull/2141)
* Core: Moved some symfony form types to the common namespace [#2142](https://github.com/forkcms/forkcms/pull/2142)
* Core: File folder names now match the casing of the modules they belong to [#2143](https://github.com/forkcms/forkcms/pull/2143)
* Core: Upgraded to symfony 3.3. [#2151](https://github.com/forkcms/forkcms/pull/2151)
* Core: Cleaned up the fork installer code [#2153](https://github.com/forkcms/forkcms/pull/2153)
* Travis: Add code style checks to travis [#1972](https://github.com/forkcms/forkcms/pull/1972)
* Github: Add header image to README.md [#2117](https://github.com/forkcms/forkcms/pull/2117)
* Blog: Cleaned up the installer [#2070](https://github.com/forkcms/forkcms/pull/2070)
* ContentBlocks: Cleaned up the installer [#2068](https://github.com/forkcms/forkcms/pull/2068)
* ContendBlocks: Updated the module structure to the domain model [#2096](https://github.com/forkcms/forkcms/pull/2096)
* Extensions: You can now switch themes using your keyboard [#2144](https://github.com/forkcms/forkcms/pull/2144)
* Extensions: Cleaned up the installer [#2071](https://github.com/forkcms/forkcms/pull/2071)
* FAQ: Cleaned up the installer [#2072](https://github.com/forkcms/forkcms/pull/2072)
* FormBuilder: Cleaned up the installer [#2073](https://github.com/forkcms/forkcms/pull/2073)
* Groups: Cleaned up the installer [#2074](https://github.com/forkcms/forkcms/pull/2074)
* Locale: Cleaned up the installer [#2085](https://github.com/forkcms/forkcms/pull/2085)
* Location: Cleaned up the installer [#2076](https://github.com/forkcms/forkcms/pull/2076)
* Mailmotor: Updated documentation [#1987](https://github.com/forkcms/forkcms/pull/1987)
* Mailmotor: Added double opt-in setting [#2005](https://github.com/forkcms/forkcms/pull/2005)
* Mailmotor: Updated the module structure to the domain model [#2145](https://github.com/forkcms/forkcms/pull/2145)
* Pages: Cleaned up the installer [#2079](https://github.com/forkcms/forkcms/pull/2079)
* Pages: Pages are grouped in a nicer way when installing with demo data [#2150](https://github.com/forkcms/forkcms/pull/2150)
* Search: Cleaned up the installer [#2081](https://github.com/forkcms/forkcms/pull/2081)
* Profiles: Cleaned up the installer [#2080](https://github.com/forkcms/forkcms/pull/2080)
* Settings: Cleaned up the installer [#2082](https://github.com/forkcms/forkcms/pull/2082)
* Tags: Cleaned up the installer [#2083](https://github.com/forkcms/forkcms/pull/2083)
* Users: Cleaned up the installer [#2084](https://github.com/forkcms/forkcms/pull/2084)

…

Removed:

* Core: The api has been removed from fork, it is now recommended to use a symfony bundle [#1981](https://github.com/forkcms/forkcms/pull/1981)
* Spoon: SpoonFilter::getGetValue and SpoonFilter::getPostValue have been removed [#2051](https://github.com/forkcms/forkcms/pull/2051)
* Spoon: The things that have been removed from spoon library can be found in its [changelog](https://github.com/forkcms/library/releases/tag/3.0.0)
* Mailmotor: Removed old library for campaignmonotor since we are using one via composer atm [#1973](https://github.com/forkcms/forkcms/pull/1973)

…


4.5.5 (2017-08-03)
------------------

Bugfixes:

* Core: Add missing generated_url_selector in the form type for the meta [#2116](https://github.com/forkcms/forkcms/pull/2116)
* Core: Add debouncer to generatedUrl to prevent spamming the server and catch events like copy/paste in addition to keystrokes [#2115](https://github.com/forkcms/forkcms/pull/2115)
* Core: Fix deletion of uploaded images not working for file and image types [#2126](https://github.com/forkcms/forkcms/pull/2126)
* Analytics: Hide analytics widgets when no internet connection is found [#2119](https://github.com/forkcms/forkcms/pull/2119)
* Composer: Update php requirements since fork 4 won't work on php 7.2 [#2140](https://github.com/forkcms/forkcms/pull/2140)
* FormBuilder: Fix required indication missing in form builder [#2156](https://github.com/forkcms/forkcms/pull/2156)
* Mailmotor: Fix mailmotor when no mail engine is chosen [#2134](https://github.com/forkcms/forkcms/pull/2134)
* Profiles: Fix avatar path in installer [#2124](https://github.com/forkcms/forkcms/pull/2124)
* Profiles: Fix profile settings missing in toArray when the settings haven't been loaded previously [#2147](https://github.com/forkcms/forkcms/pull/2147)


4.5.4 (2017-06-16)
------------------

Bugfixes:

* Github: Add mention of what to do when a security issue is found to the readme [#2030](https://github.com/forkcms/forkcms/pull/2030)
* Github: Fixed typo in the readme [#2035](https://github.com/forkcms/forkcms/pull/2035)
* Core: Fixed some typos in the analytics locale [#2028](https://github.com/forkcms/forkcms/pull/2028)
* Core: Fix html5 file input revalidation not working [#2043](https://github.com/forkcms/forkcms/pull/2043)
* Pages: Fix blog images path casing [#2104](https://github.com/forkcms/forkcms/pull/2104)
* Pages: Fix auth tab not working correctly [#2037](https://github.com/forkcms/forkcms/pull/2037)
* Triton: Fixed clicking on label in search widget didn't focus input field [#2048](https://github.com/forkcms/forkcms/pull/2048)


4.5.3 (2017-04-13)
------------------

Bugfixes:

* Core: Fix the htaccess so ckfinder works again [#2025](https://github.com/forkcms/forkcms/pull/2025)
* Core: Make sure SpoonSession is initialised before starting symfony session [#2023](https://github.com/forkcms/forkcms/pull/2023)
* Blog: Fix unpublished posts showing up in the pager of blog detail [#2024](https://github.com/forkcms/forkcms/pull/2024)
* FormBuilder: Fix formbuilder problem with label and checkbox [#2026](https://github.com/forkcms/forkcms/pull/2026)


4.5.2 (2017-03-22)
------------------
Security:

* Core: XSS and direct access to certain php files fixed [#2013](https://github.com/forkcms/forkcms/pull/2013)

Bugfixes:

* Extensions: Install after uploading a doctrine module has been fixed [#2014](https://github.com/forkcms/forkcms/pull/2014)
* ContentBlocks: Fix content block revision issue with time and user [#2012](https://github.com/forkcms/forkcms/pull/2012)


4.5.1 (2017-03-17)
------------------
Security:

* Core: updated swiftmailer to include security patch [#2011](https://github.com/forkcms/forkcms/pull/2011)

Bugfixes:

* Core: Fix redirect exception not working inside parsewidget template modifier [#1996](https://github.com/forkcms/forkcms/pull/1996)
* Core: Remove action pages from children navigation [#2009](https://github.com/forkcms/forkcms/pull/2009)
* Core: Fix meta id comparison with null [#2010](https://github.com/forkcms/forkcms/pull/2010)
* Core: Move the raw conversion to the macro itself [#2006](https://github.com/forkcms/forkcms/pull/2006)
* Core: Fixes for travis [#2003](https://github.com/forkcms/forkcms/pull/2003) and [#2002](https://github.com/forkcms/forkcms/pull/2002)
* Core: Fix image/file prefix for certain characters [#1995](https://github.com/forkcms/forkcms/pull/1995)
* Core: Replace http with https in schema.org url [#1998](https://github.com/forkcms/forkcms/pull/1998)
* Core: frontend.js is now also minified [#1990](https://github.com/forkcms/forkcms/pull/1990)
* Core: Fixed icon button layout issues [#1991](https://github.com/forkcms/forkcms/pull/1991)
* Core: Removed usages of the deprecated Language class [#1983](https://github.com/forkcms/forkcms/pull/1983)
* MailMotor: Add fixes from mailmotor/campaignmonitor-bundle [#1982](https://github.com/forkcms/forkcms/pull/1982)

Improvements:

* Installer: Nicer installer requirements page [#1992](https://github.com/forkcms/forkcms/pull/1992)


4.5.0 (2017-01-20)
------------------

Bugfixes:

* Core: Fixed exception when using Common\Language\Language in a console command
* Core: Fixed https mixed content warnings
* Core: Create relations when installing a module with doctrine
* Core: Removed annotations from documentation of AbstractFile and Image type because it gave errors when validating
* Core: Fix timezone issues with doctrine date
* Core: Meta entity no longer shows none in the meta tag for SEOFollow and SEOIndex
* Core: Fix stylesheetparser in ckeditor
* Installer: Removed stray code
* Search: Fixed live-suggest
* FormBuilder: Fixed errors when non required radio button wasn't filled in
* FormBuilder: Fix numeric html5 validation translation not working
* Pages: Fix redirect icon applied to sub pages of a page that is redirected
* Pages: Fix sitemap widget template
* Triton: Fix html not parsed in search results
* Profiles: Typo fixes in locale
* Mailmotor: Fix errors when list is empty or has no users in it

Improvements:

* Core: Added a basic implementation of the symfony form collection class with working add and delete buttons
* Core: Status fields with the value hidden are now grayed out in the data grids
* Core: Replaced bower with yarn
* Core: Updated ckeditor to 4.6
* Core: Added eps,svg,webp to the allowedExtensions of ckfinder
* Core: Added FileType for symfony form that will handle everything for you
* Core: Added ImageType for symfony form that will handle everything for you
* Profiles: Redirect back to the page you came from when logging in with the loginBox widget
* Pages: Page image is now also available in the subPages widget
* Pages: Dont index content for search if authentication for the page is true
* Pages: You can now add a subpage to a page directly instead of creating it in the root and then dragging it to the correct place 
* MailMotor: The subscribe widget is now a standalone action instead of a redirect to the full form
* Tags: Tags that have no items in this language are no longer shown
* Tags: Blog image is now available when displaying blogpost with a tag
* Github: Clarification about what people need to fill in under: Resolves the following issues


4.4.1 (2016-12-14)
------------------

Bugfixes:

* Core: Doctrine DBAL Type for date's timezone issues where fixed
* Core: Avoid duplicate slash in canonical URL
* Core: Fix ajax calls in the frontend using GET
* Core: Add missing alternate meta tags when multi language is enabled
* Core: Fix redirect loop on the 404 page that was triggered when the cache wasn't loaded.
* Pages: Fix saving pages when the profiles module is not installed
* Pages: Check if auth_groups is set
* Pages: Check if there are any auth groups
* MailMotor: Fix languages not passed correctly in events
* MailMotor: Add missing translations
* MailMotor: Fix wrong directory casing
* FormBuilder: Fix html in inputlist


4.4.0 (2016-12-02)
------------------

Bugfixes:

* Core: Fix styling when page detail buttons stand on two lines
* Core: Change "showModuleAction" variables to "allowModuleAction"
* Core: Prevent scroll to top after click a cookievar button
* Core: Add missing createForm method in the frontend
* Core: Get url for block fixes when checking with data
* Core: Also use UTCDateTimeType for date
* Core: Serialize bug in meta entity SEOFollow and SEOIndex
* Core: Add missing implementation of the tolabel filter to the frontend
* Core: The variable cookieBarHide should be global
* Core: Fix errors when clear text was passed to the translator
* Core: Fix abstractImage thumbnails not generating
* Core: Fix pagination last and first url and label
* Core: Format template modifiers
* FormBuilder: Fix mailing form shows html
* Pages: Fix previous/next navigation for hidden pages
* FAQ: Fix faq category detail link in backend
* Locale: Remove old classes on the textarea of locale edit

Features:

* Core: Add redirect method to widgets
* Core: Custom html5 validation
* Page: Authentication options with the profiles module
* MailMotor: Completely revamped module. Now also with MailChimp

Deprecations:

* Api: Fully deprecated

Enhancements:

* Core: Allow html in the alerts in the backend
* Core: Removed duplicate for "SITE_MULTILANGUAGE"
* Core: Tabs to spaces
* Core: Improve https support
* Core: Abstract file and image updates
* Core: Code style
* ContentBlocks: Renaming "getName()" to "getBlockPrefix()"
* FormBuilder: Make the language available in the frontend for formbuilder


4.3.1 (2016-11-04)
------------------

Bugfixes:

* Core: FIX send mail by a Cronjob / URL object not available
* Core: Remove devtools plugin from CKEditor
* Core: Remove updating the schema when it already exists
* Core: Fix web path of images if they are in a subdirectory
* Core: Fix ck plugin errors
* Core: https port fix
* Authentication: Fix Authentication::isAllowedAction
* Blog: Fix error's not showing when adding categories on the blog add or edit action
* Extensions: Fix themes from github couldn't be uploaded
* FAQ: Correct order in the index action for categories
* Location: Fix incorrect use of $this->header->addJS
* Core: Remove server root from loading dirs for templates
* Core: Fix twig issues with latest bugfix release of symfony 2.8
* Installer: Validate the backend credentials


4.3.0 (2016-10-20)
------------------

Features:

* Core: Make it possible to define other values for the headers X-Frame-Options, X-XSS-Protection and X-Content-Type-Options
* Core: The jquery function doMeta is now configurable
* Core: Locale ValueObject is now serializable
* Core: Add method to compare Locale
* Core: Set meta settings on module action by passing the meta entity to the method setMeta
* Core: Entity for the Metadata
* Core: Symfony form type for meta
* Core: Module Extra type value object
* Core: Make our translations available in the translator service
* Core: Base ValueObject and symfony form type for the uploading of files and images
* Console: Added a command to enable a locale
* Location: Integrated Street View
* Location: Integrated google map styles

Deprecations:

* Core: The service entity.create_schema is now deprecated in favour of fork.entity.create_schema
* Core: Deprecated our own event system in favour of symfony events
* Tools: Deprecated tools/spoon2twig in favour of using external tools
* Tools: Deprecated tools/install_locale in favour of app/console forkcms:locale:import

Enhancements:

* Core: Use FrontendModel everywhere in the Frontend TemplateModifiers instead of mixing it with Model
* Core: Remove the usage of deprecated code
* Core: Update outdated composer packages
* Core: Refactor out exit statements
* Core: Replaced dirname(\_\_FILE\_\_) with \_\_DIR\_\_
* Core: Remove unused code found by scrutinizer
* Core: Use font awesome as ajax spinner
* Core: Use margin instead of padding to space the icon from the text in a button
* Core: Update the schema instead of ignoring when the schema for the table already exists
* Core: Replaced the deprecated twig comparator sameas with same as
* Core: Updated CKEditor to 4.5.10
* Mailmotor: Don't show the debug toolbar in the edit email iframe
* Installer: Use font awesome as ajax spinner
* Analytics: Update the explanation to link a google analytics account since google has revamped the interface (again...)

Bugfixes:

* Core: Fix Symfony form errors font colour
* Core: Fix loading external css files in the frontend with the addCss method
* Blog: Fix add link on the blog index page when filtering on a category
* Tools: prepare_for_reinstall : Ignore foreign keys when deleting the tables
* Triton: Fix the minimum version
* Doctrine: Fix timezone issues
* Extensions: Fix install button not hiding on the detail page of a theme
* Mailmotor: Fix example templates (they weren't converted to twig yet)
* Mailmotor: Fix template not loading in iframe
* Mailmotor: Catch the CampaignMonitor exceptions so the ajax save returns nice error messages
* Tags: Hide the published blog posts on the tag detail if the publish date hasn't passed yet
* FormBuilder: Fix editing a form of the FormBuilder removes the edit_link on the page editor


4.2.4 (2016-10-07)
------------------

Bugfixes:

* FormBuilder: use the new instead of the deprecated language class
* Core: Common\Language didn't use the new language class yet
* ContentBlocks: When copying pages from one to another locale all the new content blocks got the same id
* Tools: spoon2twig : fix conversion spoon_date
* Tools: spoon2twig : fix conversion substring
* Tools: spoon2twig : fix geturlforblock
* Tools: spoon2twig : fix getnavigation
* Tools: spoon2twig : fix getsubnavigation
* Tools: spoon2twig : fix words like contact being detected as an action
* Tools: spoon2twig : fix Mail subdirectory not converting
* Tools: spoon2twig : fix converting positions
* Tools: spoon2twig : ucfirst is no longer replaced with capitalize since it is not the same and has been added to fork
* Tools: spoon2twig : fix multiple translations on the same line messing things up
* Github: point to slack instead of irc for support
* Extensions: fix install link on detail page of a module
* Core: Fix word-wrap problem in tooltips

4.2.3 (2016-09-23)
------------------

Bugfixes:

* Api: fixed the build in client
* Console: fix using backend language
* FormBuilder: prevent editing multiple fields at the same time
* FormBuilder: submit the field when hitting enter
* FormBuilder: fix classes not applied to form fields
* Core: fix button detection of datagrid columns was case sensitive
* Installer: fix extensions module not installed as first module
* Twig: fix hide page title
* SpoonLibrary: updated to fix price check on text field
* Core: made the explanation better when the info.xml is missing
* Core: updated matthiasmullie/scrapbook to fix errors with redis


4.2.2 (2016-09-02)
------------------

Bugfixes:

* Core: Fix ckeditor in bootstrap modal
* Core: Add simple-bus/doctrine-orm-bridge to fix assetic errors 
* Core: Bugfix phpcs failing module tests
* Locale: Make sure the names of the translations start with a capital letter
* ContentBlocks: Make sure that extra things added to the data of a content block aren't lost when updating
* ContentBlocks: Remove the tightly coupling between authentication and content blocks
* Twig: Show warning that cookies are required in the backend
* Twig: Make sure the real data is loaded when using the template modifier parsewidget
* Installer: Fix deselecting modules during installation
* Installer: Make sure we can return in the installer

Enhancements:

* Core: Style fixes


4.2.1 (2016-08-31)
------------------

Bugfixes:

* Blog: dashboard widget link fixed
* Core: fix duplicate use statements


4.2.0 (2016-08-29)
------------------

Features:

* Core: added Doctrine
* Core: added Symfony Form
* Core: added SimpleBus as command bus
* Core: updated CK Finder to 2.6.2
* Pages: added option to set a banner image on a page
* Profiles: added check when setting new password that it is typed correctly
* Profiles: generated pages on install are now translated
* Core: deprecated the push notifications

Enhancements:

* Core: clean up phpdoc
* Core: code quality improvements

Bugfixes:

* Faq: added missing locale
* Core: missing use statement in FormFile


4.1.2 (2016-08-26)
------------------

Bugfixes:

* Location: fix fetching coordinates.
* Core: make sure our vendor folder can't be accessed.
* Core: show file size when uploadeded file size is too big.
* Core: remove useless variable assignement.
* Core: change some exit statements to exceptions.
* Core: update symfony to 2.8.9 (to include security fixes).
* Core: remove obsolete validation.
* Installer: remove non existent mailer service.
* Profiles: replace spoon directory with the filesystem component.
* Core: improve some UI issues.
* Core: improve cache handling (during both installation and other places).
* Core: add the translation for German.
* Core: protected some more files using htaccess.
* Search: show "add synonym" link when there are no synonyms yet.
* Core: fix incorrect language variable.
* Core: fix pagination labels.


4.1.1 (2016-08-10)
------------------

Bugfixes:

* MailMotor: Mailmotor was broken when it wasn't configured yet
* Core: When switching languages you no longer get Item not found messages
* Core: Fork CMS now works on mysql 5.7, some queries broke on the default configuration

Enhancements:

* Add extra key-words to composer.json
* Composer: irc has been removed as support option and slack has been added


4.1.0 (2016-08-08)
------------------

Features:

* Location: added Google Maps API key
* Core: multiple og:image:width and :height
* Core: change urlencoding from RFC 1738 to RFC 3986
* FormBuilder: Anchor added to form widget
* Location: added zoom-leven 1 and 2
* Github: added issue and pull request templates
* Core: added TemplateModifier showBool alias to DataGridFunctions
* Profiles: added SecurePage widget
* Core: getUrlForBlock now takes data into account
* Core: page parameter can now be changed in the pagination query
* Twig: added a macro for the required asterisk and tooltip

Bugfixes:

* CK Finder: fix non existent service session.handler
* Profiles: make sure the settings are loaded in cache before a new setting is set
* Location: fix settings not loading correctly because twig escaped the html


4.0.6 (2016-07-29)
--

Bugfixes:

* Pages: fix casing issue.
* Core: add missing yaml config to the editorconfig file.
* Core: make the backend more anysurfer compliant.
* Pages: make filesystem checks more robust for og:image tags.
* Faq: use label instead of hardcoded text after sequence success.
* FormBuilder: only show the success message for the submitted form.
* Core: fix cache sharing between environments.
* Core: fix issues on PHP 7.1.
* Core: fix issues when widgets mixed up content.
* Core: fix double encoded ampersand in pagination urls.
* Pages: fix bad contrast when showing hidden page blocks.
* FormBuilder: fix paragraph and heading that weren't editable.
* Core: fix template bug in pagination.
* Core: make sure emails can be themed.
* Core: make sure diactrics will be showd correctly in twig.
* Groups: make sure we can hide dashboard widgets for groups.


4.0.5 (2016-07-12)
--

Bugfixes:

* Location: show google maps key when Location is installed.
* Core: add needed swiftmailer monolog configuration.
* Core: make the formatCurrency modifier respect the number format.
* Core: fix deletion of cookies.
* Core: fix case error.
* Core: fix "exists" instead of "exist" in exceptions and comments.
* Core: fix wrong order of twig filters.
* Core: fix wrong css class on help-blocks.
* Core: fix link in cookie bar text in the triton theme.
* Core: use php.ini's default session location.
* Extensions: fix install button for installed modules.
* Core: minify module JavaScript files.


4.0.4 (2016-07-01)
--

Bugfixes:

* Analytics: fixes for tracking.
* Locale: fix removing entries.
* Core: fix camelcase instead of ucfirst.
* Installer: fix unreadable text.
* Core: also use browser cache on files with uppercased extensions.
* Analytics: refactor to avoid some bugs.
* Core: small layout fixes.
* Core: fix image delete with a subfolder.
* Core: use non breaking spaces in format currency.
* Core: fix the showBool template modifier.
* Core: cleanup fallbacks for legacy browsers.
* Core: remove @author tags in favor of git history.
* Core: remove twig generation notices.
* Analytics: fix widgets when there is no data yet.
* FormBuilder: fix placeholders.
* Core: fix a lot of emails and template issues.
* Core: fix navigation with deeper levels.
* Core: fix multiple widgets with different content on one page.
* Core: make sure modules folder isn't required in a theme.
* Core: fix page titles containing html entities.
* Core: fix overflowing logs in production mode.
* Core: fix unsupported date formats.
* Core: fix double slash in the url causing 404's.
* Core: fix wrongly placed form errors.
* Core: fix usage of deprecated sprintf filter.
* Core: fix reference to the deprecated forum.
* Core: fix an issue when navigation didn't contain pages.
* Core: hide the navigation after the animation.
* Profiles: show the profiles filter titles again.
* Groups: fix javascript issues with the group rights.
* Groups: fix shown tabs when a user has no rights to it.
* Groups: fix some coding styles.
* Core: fix the sub navigation with too many entries overflowing the page.


4.0.3 (2016-06-15)
--

Bugfixes:

* Core: translate labels in the backend menu
* Extensions: fix deletion of modules extras
* Core: fix limit on key length of 1000 bytes during installation
* Core: fix breadcrumb on single language sites
* Core: fix resequencing in Safari


4.0.2 (2016-06-10)
--

Bugfixes:

* Core: remove duplicate swiftmailer config.
* Core: cleanup unused use statements.
* Core: fix some phpdocs.
* Search: fix the livesuggest action.
* Core: fix typo: UT8 instead of UTF8.
* Core: remove obsolete parameters on the getContent method of templates.
* Core: fix editorconfig for twig files.
* Core: fix indentation in twig files.
* Extensions: fix styling of "new themes".
* Core: fix references to tpl files in triton's info.xml file.
* Faq: make sure the feedback works in Safari.
* Core: include bower components in this repository.
* Core: take the language into account when fetching the 404 url.


4.0.1 (2016-06-07)
--

Bugfixes:

* Core: Fixes date field with empty date.
* Core: Fix lost ucfirst template modifier.
* Installer: Make "same language" not required.
* Core: Fix the THEME_URL constant referencing the backend.
* Core: Fix the hasColumn function of datagrids.
* Core: Fix the not-matching color of the fork logo.
* Core: avoid &nbsp; in favor of spaces.
* Extensions: improve icons.
* Blog: Fix preview url in edit action.
* Core: Fix asterisk changed by # in the frontend.
* Users: Never use the (not created) source file of avatars.
* Core: Improve the gitignore file.


4.0.0 (2016-05-27)
--

Improvements:

* Core: Twig is now used everywhere
* Core: Backend is now using bootstrap
* Core: new backend device
* ...

Bugfixes:

* Blog: Fixed saving Blog articles as draft.
* Core: Fix incorrect path for theme OpenGraphImage.
* Core: Fixed when deploying, that /src/Frontend/Cache/Navigation/editor_link_list_x.js is now created if not exists.
* Faq: fixes faq-category sequence reordering not being saved.
* Pages: removed single quotes converter in CacheBuilder. This fixes page titles with single quotes.
* Pages: Fixes "Notice: Undefined index: parent_id" when viewing a page revision where the page is located in the root of the pages tree.
* Pages: Fixes "Error: Call to a member function addMetaData() on null" when viewing an existing revision page.
* Core: BackendDataGridFunctions::showImage updated with url, with and height.


3.9.6 (2015-12-22)
--
Improvements:

* Core: Adds a function to get checkbox enum values easier.
* Core: use mod gzip on json files.
* Settings: add start of body scripts.
* Profiles: add missing labels.
* Locale: speed up index and analyse actions.
* Core: use PSR-6 compatible caching (with an external package: scrapbook).
* Core: use Flysystem as the cache backend.
* Core: replace jquery ui with typeahead & bloodhound.
* Console: make the Symfony container available.
* Core: update Spoon Library.
* Analytics: improve coupling your analytics account.
* Core: make the url of the last breadcrumb item available.
* Installer: only load installer when Fork is not installed (and in test environment).
* Location: use https for Google maps by default.
* Locale: create a Symfony command to install locale.
* Locale: allow us to install locale for a module.
* Core: add the language to the canonical url.
* Core: upgrade Symfony to version 2.8.
* Core: bump the minimum php version to 5.5 and allow php 7 too.
* Ajax: add testcases for invalid ajax requests.

Bugfixes:

* Pages: Don't get hidden pages with getUrlForBlock.
* Pages: Fix 500 error when accessing hidden pages.
* Console: use constants correctly.
* MailMotor: avoid catching redirect exceptions.
* Pages: fix array to string conversion.
* Profiles: fix the import action.
* Core: don't allow access to the .git folder.
* Ajax: fix exceptions that should be handled.
* Core: fix case mismatch in DataGrid classes.
* FormBuilder: fix default value containing a space.


3.9.5 (2015-08-31)
--
Improvements:

* Extensions: fixed UX-error where "add-theme button" was not aligned on the right side.
* Travis: run on new container based infrastructure

Bugfixes:

* Frontend: no more duplicate JS-files.
* Analytics: Don't load dashboard widgets when not configured yet.
* Core: Fix return types for addImage methods in forms.
* Core: remove safe_mode directive.
* Pages: only escape single quotes in the cache files
* Core: check authentication before showing user links


3.9.4 (2015-07-09)
--
Improvements:

* Core: Moved startProcessingHooks from Backend/Frontend to Common/Core/Model.php
* Core: Save logs in an environment specific log file.
* Extensions: allow more different zip formats for module uploads.
* Locale: add a cli tool to import locale.
* Core: move phpunit to the root of the project.
* Core: save logs in environment specific files.
* Analytics: improve usability + add functionality
* Core: improve exception messages.
* Core: remove the unused timezones table.
* Core: remove unneeded require statements.
* Core: add a datagrid modifier to display boolean types.
* Core: bump minimum PHP version to 5.4

Bugfixes:

* Core: Fixed exporting .csv files.
* Analytics: Don't let Google_Client save files in the /tmp/Google_Client directory
* Core: Fixed CamelCasing issues with Spoon classes SpoonDatagridSourceArray, SpoonDatagridPaging & iSpoonDatagridPaging.
* Core: fix composer install on windows.
* Blog: fix image deletion for revisions.
* Core: fix exporting csv files


3.9.3 (2015-06-10)
--
Improvements:

* Improve the inheritance of code and avoid duplicate code.
* FormImage has now mime type hinting so only images will be visible in the file-dialog.
* `composer-create-project` is now the default way of installing fork.
* Added some headers to increase the security.
* Only inject the modulesSettings in the configurator.
* Some minor updates on the PHP documentation.
* Refactored the modules settings to live in a service.
* Indicate the default action when doing a prepare to reinstall.

Bugfixes:

* Analytics: Auth config content saved in databse, because capistrano deployments didn't work with BACKEND_CACHE_PATH.
* Api: increasing security when user is GOD.
* Core: fix generating meta url with special characters.
* Core: fix some authentication issues.
* Core: year, month and day are now passed to the datepickers
* Core: Frontend input date fields reformats date incorrect
* Core: fix issue with the pagination-urls
* Api: Fixed namespaces
* Formbuilder: Fix "Illegal offset type" error on setReplyTo method
* Core: Fixed parse method compatibility with Spoon library release 1.3.17
* Core: Fix exception handlers by using "self" instead "this"


3.9.2 (2015-05-12)
--
Improvements:

* Core: every template can now check if it has a certain parent id with {option:isChildOfPageX}
* Locale: improve performance of the index page
* Core: reduced database queries in BackendDataGridFunctions::getUser().
* Core: Replace SITE_MULTILANGUAGE with $container->getParameter('site.multilanguage')
* Core: Replace SPOON_DEBUG_EMAIL with $container->getParameter('fork.debug_email')
* Core: Replace SPOON_CHARSET with $container->getParameter('kernel.charset')
* FormBuilder: use the event dispatcher from Symfony to send the email
* Core: allow installed modules to subscribe their own configuration/services
* Core: enable gzip compression on svg files
* Core: rename BlockIsHTML to BlockIsEditor

Bugfixes:

* Core: Fixed bug with decoding in truncate modifier
* Core: Fixed encoding ampersand for action url
* Groups: Fix (in add/edit) for executing widgets for which the module doesn't exists.
* Mailmotor: Fix wrongly cased classname
* Blog: make sure images can get reverted together with their revision
* Formbuilder: Fix reply option


3.9.1 (2015-03-12)
--
Improvements:

* Core: Replace SPOON_DEBUG with $container->getParameter('kernel.debug')
* Formbuilder: Add placeholders to textbox and textarea elements
* Mailmotor: add missing "SubscribedOn" label

Bugfixes:

* Core: Fix undefined variable $message
* Core: Fix the mailer transport to get instantiated correctly
* Installer: Fix checked paths in first step


3.9 (2015-03-05)
--
Improvements:

* Settings: test email connection with SwiftMailer.
* Formbuilder: added the possibility to add date & time fields.
* Settings: test email connection with SwiftMailer
* Core: refactor out SELF constant
* Core: removed the Facebook-class-dependency
* Core: added an option to truncate a string without breaking words
* Blog: add functional tests for the frontend.
* Faq: add functional tests for the frontend.
* Search: add functional tests for the frontend.
* Authentication: add functional tests for the backend.
* API: add functional tests.
* Core: add unit tests for some template modifiers
* Core: build Fork using continious integration with Travis CI.
* Core: upgrade jQuery to version 1.11.3
* Locale: load all cache from json
* FormBuilder: reply to email can now only be put on an email field
* Tags: improve the alt text for the "remove tag" button by including the tag name
* Core: redirect using an exception instead of an exit statement
* Core: update the included Facebook SDK to v4
* Core: refactor out BACKEND_MODULE_PATH constant
* Core: use the swiftmailerbundle instead of our custom implementation

Bugfixes:

* Core: fix not correctly thrown exception
* Formbuilder: quotes and special chars are now allowed in values for radiobuttons.
* Core: the hash is now included when it is used in a form, so on submit it
    should automagically go to the form.
* Core: make sure mails with encryption can be send trough SMTP
* Core: add a .htaccess in the app dir to block all access
* Locale: fix updating locale trough ajax when no application is set
* Core: make sure bugemails work again


3.8.7 (2015-02-13)
--
Improvements:

* Mailer: use SwiftMailer to send messages (see <UPGRADE_3.9.md>)
* FormBuilder: jump to location of form after submission.
* Core: Update CKFinder to version 2.4

BugFixes:

* Locale: Avoid "Using $this when not in object context" when using php 5.3.


3.8.6 (2015-02-03)
--
Improvements:

* Core: Refactor the mailer to use SwiftMailer

Bugfixes:

* Users: Fixed error in backend user edit, when updating your own account.
* Core: Fix some wrong template names
* Dashboard: Fix casing issue in alter sequence
* Pages: Redirects are now available in the pages cache file
* Translations: Fix an SQL Injection vulnerability


3.8.5 (2015-01-14)
--
Improvements:

* Core: use Symfony Intl component instead of Spoon to fetch countries.
* Core: implement Google's sitelinks searchbox

Bugfixes:

* Core: Make sure logs aren't publically accessible
* Core: Make sure PHP 5.5+'s opcode cache is now cleared after updating code


3.8.4 (2014-12-26)
--
Improvements:

* Core: Make sure setDebugMessage is only called once.

Bugfixes:

* Pages: Fix missing variable.
* Blog: Capitalize module and detail names to fix URLs.
* Security: Avoid XSS by not directly injecting $_GET parameters in html.


3.8.3 (2014-12-12)
--
Improvements:

* Core: BackendModel::deleteModuleSetting() added.
* Pages: add has_children variable to page array.
* Location: when routing from A to B, <a href="URL"> has to change also, fixes #741.
* Core: Integrated addRssLink() function, fixes #841.
* Blog: making use of the new $this->header->addRssLink() function, fixes #841.
* Core: minifier updated to a newer version.
* Core: remove unneeded kernel.charset parameter from the parameters.yml file.
* Core: refactor the URL classes to use the Symfony Request object.
* Core: update the PHPDocs for some methods.
* Pages: add a has_children variable to pages.
* Core: add priority groups to add JavaScript in a certain order.
* Blog: implement Twitter cards.
* Extensions: check if a template file exists when adding/editing templates.
* Locale: improve the filtering and export for translations.

Bugfixes:

* Tags: fix wrong variable name.
* Core: fix installation with different interface langauge(s).
* Core: fix for dashboard ajax functions.
* Core: make sure emails can be send from the backend.
* Core: make setting cookies work on a domain with a custom port (not port 80).
* Users: when a user becomes "non-active" remove his sessions so he gets logged out.
* Faq: fix the highlighted row after adding or updating a question.
* Faq: update edited_on date after processing feedback.


3.8.2 (2014-10-21)
--
Improvements:

* Core: small fix for mail template style on very small screens.
* FormBuilder: reduced database querying while getting form fields in Form widget.
* Faq: Categories widget added.
* Core: update minify library.
* Core: update CKFinder.
* Install: refactor installer to a Symfony Bundle.
* Core: add assetic to Fork
* Core: add Twig to Fork

Bugfixes:

* Analytics: fixed CSS on servers which listens to Capital A in Analytics.css
* Core: moved KernelLoader.php to autoload.php because doctrine from CLI had problems.
* Faq: fixed deleteCategoryAllowed
* Profiles: profiles.js not exists, so don't load it in.


3.8.1 (2014-08-22)
--
Improvements:

* Core: Twitter username from settings gets parsed to template as TWITTER_SITE_NAME
* The sequence field for the extra's is now respected, see #828.
* Blog: remove the FeedBurner integration as FeedBurner is no longer active, fixes #693.
* Authentication: make the isLoggedIn function more efficient.

Bugfixes:

* Location: correct item is highlighted after updating the map, fixes #798.
* Installer: avoid duplicate headers in the installer


3.8.0 (2014-08-14)
--
Improvements:

* Profiles: mass import for profiles using a .csv added.
* Core: BackendModel::insertExtra() added to allow inserting homepage/widgets/blocks.
* Core: insertExtra Integrated in the modules: "ContentBlocks, Faq, FormBuilder and Location"
* Core: Restyled mail templates, simple fluid design (looks good on small and wide screens).
* Debug mode and environment are set earlier in the response.
  You can set debug mode with ````SetEnv FORK_DEBUG 1````
  You can set dev environment with ````SetEnv FORK_ENV dev````
* Core: when in debug mode and in dev environment, the SymfonyWebProfiler is shown in the bottom of the page.
* Core: handle errors in debug mode by the symfony error handler.
* Analytics: implement event tracking for universal analytics
* Faq: BackendFaqModel now uses BackendModel::deleteExtraById() and BackendModel::updateExtra().
* ContentBlocks: BackendContentBlocksModel now uses BackendModel::deleteExtraById() and BackendModel::updateExtra().
* Location: BackendLocationModel now uses BackendModel::deleteExtraById() and BackendModel::updateExtra().

Bugfixes:

* Core: event subscriptions did not get fired in the frontend.
* Authentication: avoid unnecessary dabase calls for unauthenticated users.
* Tags: make sure the same tag can't exist with and without a capital letter.


3.7.3 (2014-08-08)
--
Bugfixes:

* Installer: make sure our database is initalized as utf8
* Installer: remove the cached container after installation


3.7.2 (2014-07-31)
--
Improvements:

* Profiles: LoginLink widget added.
* Profiles: Added password verification field, see #695.
* Location: BackendLocationModel::getCoordinates() added.
* Extensions: you can upload a module from a zip with an extra directory
* Ajax: endpoint has been changed to not contain an extension. /src/Backend/Ajax.php is now /backend/ajax and /src/Frontend/Ajax.php is now /frontend/ajax
* Cronjob: endpoint has been changed to not contain an extension. /src/Backend/Cronjob.php is now /backend/cronjob.
* Routing: use the Symfony routing component to replace routing.yml
* Core: implement the SymfonyFrameworkBundle to handle routing.
* Core: make the AppKernel more similar to Symfony's kernel.
* Core: add the Symfony console component.

Bugfixes:

* Faq: deleting a faq question now also deletes the meta record.
* Analytics: Cronjob now throws exception instead of trying to redirect.
* BackendModel: createURLForAction now works in a Cronjob, fixed #513.
* Core: Fix generation of url's containing non-ascii characters


3.7.1 (2014-07-10)
--
Improvements:

* Core: BackendModel::updateExtra() now has a serialization check when key === 'data'.
* Blog: show image on preview
* Core: add .editorconfig file

Bugfixes:

* Locale: problem when saving Frontend locale fixed #744.
* Core: Mailer uses \Exception.
* Core: Frontend.js ajax url fixed
* Core: Loading editor templates fixed, see #747
* Analytics: Fixes action names to get data from Google Analytics, see #755
* Extensions: you can now install custom themes again.
* Pages: widget previous-next fixed.
* Extensions: using 'Core' instead of 'core'.

3.7.0 (2014-04-24)
--
Improvements:

* Core: Spoon registry has been refactored out in favor of the Symfony DI container. See UPGRADE_3.7.md for more info.
* Core: Don't throw exceptions in production mode on non-existing files.
* Core: Implemented a cookie-bar, see http://www.fork-cms.com/blog/detail/the-cookie-bar for more information.
* Core: use correct/new Facebook-js-snippet.
* Users: more logical way of handling user-permissions, see #684.
* Content blocks: only grab needed fields, see #669.
* Core: better description for CKFinder maximum image size settings.
* Core: used namespaces, see UPGRADE_3.7.md for more info
* API: use isAuthorized() instead of authorize(), see UPGRADE_3.7.md for more info.
* Core: CommonCookie and CommonUri are now in the src/Common folder
* Core: unused function BackendModel::imageSave is removed in favor of generateThumbnails().
* Core: removed duplicate mailer code and make the mailer a service

Bugfixes:

* Correct amount of sample comments in blog
* msgSequenceSaved was missing from core installer.
* Core: Modified misleading text about CKFinder maximum image size setting.
* Share with linkedin, fixed double url encoding.
* Faq: getByTags did not work in backend.
* Blog: fixes an issue where an incorrect revision could be used instead of the most recent one, see #680.
* API: use DIRECTORY_SEPARATOR instead of hardcoded /, fixes #682.


3.6.6 (2014-01-15)
--
Improvements:

* Blog: Import wordpress action added.
* Profiles: event ‚after_logged_in’ triggered when profile has logged in.
* Users: event ‚after_undelete’ triggered when a deleted user was restored.
* Core: Don't expose the path when calling ajax.php directly in non-debug-mode.
* Core: Better error handling for module rights.
* Various dutch translations updated.
* Extensions: add export of theme templates (XML).

Bugfixes:

* BackendModel: getURLForBlock can now return the url when locale is not yet activated.
* Urls containing md threw a 403 forbidden error.
* Syntax error in FrontendBlockWidget fixed.
* Some ajax files gave a syntax error as a result of merge conflicts.
* Mailmotor: Form token for widget fixed.


3.6.5 (2013-10-09)
--
Bugfixes:

* Form builder: Reply-To field flag was not saved
* Tags: Auto completing has to take language into account
* Pages: During page copy, the tags were not created in the target language.


3.6.4 (2013-09-25)
--
Bugfixes:

* Couldn't use terminate function not yet.
* CamelCased API functions for Apple and Microsoft.


3.6.3 (2013-09-25)
--
Improvements:

* Simplified getting backend settings
* Only show tagbox when users has rights
* Terminal event triggered after response
* Composer: Readme suggests using the optimise option now

Bugfixes:

* Google Tracking: Don't ignore target on outbound links
* Mailmotor: Export of selected addresses fixed


3.6.2 (2013-09-11)
--
Improvements:

* Locale: Added Greek as supported language
* Locale: Several language updates
* Analytics: Better event tracking
* FAQ: Category questions widget added
* Mailmotor: Subscribe widget uses form token

Bugfixes:

* Location: Creating a new location gave an exception


3.6.1 (2013-08-20)
--
Improvements:

* Form-builder: Reply-to checkbox added
* Blog: hide navigation when there are no items to show.
* Profiles: a user can now upload his avatar in Frontend and we can also integrate the avatar in a Backend DataGrid. Fallback for avatar is Gravatar.
* Speed enhancements
* Don't throw exceptions in production mode on non-existing files.
* Check if .htaccess file is properly uploaded
* Do not expose composer and markdown files to the outside.

Bugfixes:

* Output should be last command in ajax requests.
* Mailmotor: invalid HTTP status codes were used causing the AppKernel to throw exceptions.
* Authentication: do not allow God users to access uninstalled modules.
* Analytics: Tracking code wasn't set.
* Users: do not wrap delimiters in an array.
* Duplicated header 'content-type' fixed



3.6.0 (2013-06-18)
--
Improvements:

* Core: introduction of the Filesystem component, see UPGRADE_3.6.md.
* Core: introduction of the Finder component, see UPGRADE_3.6.md.
* Removed "thx to" from CHANGELOG.md; changelog is for change announcements, attributions are in git log.
* Analytics: let the user chose between GA & DC for tracking-code.
* Analytics: added the possibility to choose Universal analytics, which is also the new default.
* The backend in Internet Explorer doesn't need to be emulated anymore.
* Core: Upgraded Highcharts to 3.0.2

Bugfixes:

* Core: jQuery-plugins should escape data when using the raw data.
* Security: prevent CSRF.
* Mailmotor: fixed linking your account.
* Core: remove all entities instead of just the special chars before truncating a string. Fixes #386.
* Groups: double usage of variable cause unexpected behavior.
* Core: don't reassign values when passing them to Akismet.
* Blog: getRelated now listens to $limit.


3.5.1 (2013-04-15)
--
Improvements:

* Symfony: upgrade components to 2.2.
* Core: isInstalledModule() added in BackendModel.
* Core: use remote html5-shiv.
* Core: mailer supports SSL/TLS from now on.
* Analytics: better grouping for Google Analytics profiles.
* Core: deleteThumbnails() added in BackendModel.
* Core: Minify is now installed with Composer.

Bugfixes:

* Core: faulty Chinese translations fixed.
* Extensions: removed deprecated getDB().
* FormBuilder: removed deprecated getDB().
* MailMotor: CampaignMonitor wrapper class could not be loaded due to a faulty include path.
* Installer: after removing the install folder an errors was throw when accessing the /install url.
* Installer: after sending Location headers we need to exit to prevent further execution of the application.
* Core: do not add headers set by Spoon to Response. Otherwise they will be send twice.
* Core: removed line of code from frontend pagination.
* Spoon: SPOON_DEBUG level did not reflect the parameters.yml settings.
* Email: allow null as plain_text value to prevent MySQL errors to be thrown. Fixes #429.
* Share-widget: fixed the whitespace added by Pinterest. Fixes #392.
* Core: CKEditor is nov available in Chinese. Fixed #381
* Extensions: Removed html entities out of header.
* Installer: used correct path for checking if Fork is installed.
* Core: Login sql error on wrong email fixed.
* Location: Address in widget fixed.


3.5.0 (2013-03-13)
--
Improvements:

* Core: CommonUri added so we can generate a safe filename and url. Tx to Jeroen Desloovere
* Core: Upgraded to CKEditor 3.6.6
* Core: Upgraded to CKFinder 2.3.1
* Core: added utils.string.sprintf to backend and frontend.
* With the 3.5.0 release, Fork CMS will be available under the MIT-license.
* Core: allow people to define their own error handler.
* Core: switched to the official Facebook SDK, inspired on the pull request of Jeroen.
* Start using Composer to handle dependencies. See more info in the README.md.
* Core: Akismet and CssToinlineStyles are now installed with Composer.
* Core: Upgraded Highcharts to 2.3.3
* Core: Upgraded to jQuery UI 1.8.24
* FormBuilder: do not prefix the site URL to the form action to prevent submitting to another domain.
* Core: starting to use namespaces for the external classes that use namespaces.
* Core: upgraded Spoon
* Core: new CKFinder license, see: http://www.fork-cms.com/blog/detail/new-ck-finder-license
* Core: merged all autoloaders in to one autoload.php.
* Core: added the Symfony HttpFoundation and HttpKernel components via an AppKernel.
* Core: added the Symfony DependencyInjection component to handle our services and config.
* Core: the AppKernel is passed to all actions/models which contains the DI container.
* Core: one frontcontroller which routes all requests (actions, ajax, cronjobs, ...)
* Core: replaced globals*.php config files with app/config/config.yml.
* Core: removed js.php
* Spoon: Spoon dependency is now handled via composer.
* Core: Include a non-official patch for CKeditor to fix an issue with the stylesheet-parser on FF/Safari on Macs.
* Blog: enabled Flip ahead for blog-posts.
* Core: enabled Flip ahead for paginated pages.
* Core: Pagination can now use an anchor.
* Core: Added validation for module and action in the frontend ajax.
* Core: added $action to BackendModel::getExtrasForData + deleteExtrasForData.
* Core: getUTCTimestamp() added in FrontendModel.
* Core: Replace getDB() in the models with getContainer()->get('database')
* Core: Pagination can now use an anchor.
* Core: added $action to BackendModel::getExtrasForData + deleteExtrasForData.
* Core: Added validation for module and action in the frontend ajax.
* Core: getUTCTimestamp() added in FrontendModel.
* Core: Pagination for 6 pages showed 7 instead.
* Tags: FrontendTagsModel::get() should use FRONTEND_LANGUAGE.
* Pages: Widget had invalid parent url
* Blog: Show always Open Graph Tags
* Pages: BackendPagesModel::copy() added, so it can be called from elsewhere.

Bugfixes:

* Users: Added fix so users can't edit other profiles.
* SpoonDate: only replace full matches of date abbreviations, otherwise Montag becomes Mo.tag.
* DataGrid: do not overwrite existing row attributes when greying out a row.
* Form: encode html entities in hidden field values to prevent XSS.
* Mailmotor: add jsData to iframe template.
* Location: Google Maps JS needs to be loaded before location.js.
* Core: when fetching parameters take the index in account when computing the differences.
* Blog: Use full links for the navigation below the blog-posts.
* FormBuilder: validation (email, numeric) was inherited from previously added fields causing errors on checkboxes.
* Blog: Ticket 294: Next and previous don't work when blog-items has same publish_on date
* TagBox: Ticket 333: Tags should be handled as strings
* Extensions: Ticket 316: Link to default action
* API: Fix bug in form_builder.entriesGet where limit/offset would be applied to fields instead of the form submissions.
* Locale: Fix jsBackend.locale.get() so the {$loc...} labels get fetched correctly.
* Core: A search term should only be saved when it's not empty
* Core: BackendModel::invalidateFrontendCache() should listen to the given language.


3.4.4 (2012-09-12)
--
Improvements:

* Location: Fixed location widget. When debug = false, google wasn't loaded correctly.
* Users: User can't change its own rights when not allowed to view the index.
* Core: Upgraded Highcharts to 2.3.2
* Core: Upgraded CKFinder to 2.3.0
* Form builder: added API-methods.
* FAQ: Setting for "one category" added, so user only has 1 category in the website + the category title is hidden in the frontend (for smaller websites).
* Core: removed ini_set's. Let the server handle this.

Bugfixes:

* Mailer: The names are now decoded, so bugs with apostrophes in names are fixed.
* Analytics: all calls now require an API key as is described in the migration to Gdata v2.4 on https://developers.google.com/analytics/resources/articles/gdata-migration-guide.
* Themes: templates extras_data from other languages was overwritten.
* Themes: incorrect block index was set when deleting a position causing all default blocks to be unlinked.
* Core: exceptions were not displayed on CLI when SPOON_DEBUG was off. A minimal debug message was added.


3.4.3 (2012-08-28)
--
Improvements:

* Core: Added functions to manage modules_extras, can be used for custom widgets.
* Core: Upgraded to jQuery 1.8
* Core: Upgraded to jQuery UI 1.8.23
* Blog: Added blog.comments.delete in the API.
* Core: return-format for the API can be specified through Accept-header, GET or POST.
* Core: jQuery sharing widget will now merge options recursively (deep copy).

Bugfixes:

* Core: fixed some issues related to PHP 5.4.
* Locale: fixed locale.js conflict with backend.js.
* Core: use language parameter when rebuilding cache, instead of unavailable constant.
* Pages: issue when changing themes, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/314.
* Pages: hidden pages weren't visible in the tree.
* Core: backend used working language instead of interface language for javascript translations.
* Core: editor-templates weren't loaded because the language wasn't set at the point the default config is defined.


3.4.2 (2012-07-31)
--
Improvements:

* Core: Upgraded to jQuery UI 1.8.22


3.4.1 (2012-07-24)
--
Improvements:

* Core: Upgraded to CKEditor 3.6.4
* Profiles: rewrote method for inserting/multiple settings.
* Profiles: ask a display name in the register-step.
* Profiles: redirect to login if the profile isn't logged in on a settings-page.
* Profiles: added a modifier for fetching a profile setting.

Bugfixes:

* Core: applied http://dev.ckeditor.com/ticket/8832 to the stylesheet-parses because CKEditor triggered an JS-error in FF14.
* Core: extra validation for jsBackend.locale.get.
* Analytics: collecting live data wasn't working on iOS-devices, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/311.
* Blog: feedburner-url wasn't used in the widget, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/308.
* Core: JS messages were triggered before the document was ready.


3.4.0 (2012-07-17)
--
Improvements:

* Core: Upgraded CKFinder to 2.2.2
* Core: new way of passing data/locale into javascript, see: http://www.fork-cms.com/knowledge-base/detail/passing-data-from-php-to-javascript.

Bugfixes:

* Mailmotor: couldn't select a template in IE7/IE8 because hidden elements can't be targeted.
* Pages: fix a bug where draft versions couldn't be deleted.
* Core: Use the title of the active page record in the editor link list.
* Core: share-plugin wasn't using the correct URL for LinkedIn-shares.
* Analytics: remove GA webproperty id when unlinking your GA account. This caused a tracking code to be set even if the account was unlinked.


3.3.13 (2012-06-12)
--
Improvements:

* Core: Upgraded to jQuery UI 1.8.21
* Core: Upgraded Highcharts to 2.2.5

Bugfixes:

* Mailmotor: don't use array_unshift to get the campaigns since this will create a new array and thus new keys.
* Mailmotor: don't use the send_on column, use sent instead (send_on is renamed to sent).


3.3.12 (2012-06-05)
--
Improvements:

* Core: Generate thumbnails based on the folders in the given path, see http://www.fork-cms.com/knowledge-base/detail/generate-thumbnails-based-on-folders.
* Blog: better layout for the image-box.
* Core: upgraded Highcharts to 2.2.4
* Profiles: base the URL on the display-name instead of the id.
* Core: default extension and mime-type-validation for image-field.
* Core: made it possible to set cookies with the utils.js (Remark: not compatible with SpoonCookie)
* Core: minifier will always include svg & woff as raw data.

Bugfixes:

* Minify: first convert images to base64, then remove all whitespaces. Otherwise some image urls are not converted.
* Form builder: date start & date end were wrong after sorting, as mention on http://forkcms.lighthouseapp.com/projects/61890/tickets/303.


3.3.11 (2012-05-29)
--
Bugfixes:

* Core: fixed typo in locale which failed the import of the initial labels


3.3.10 (2012-05-29)
--
Improvements:

* Sitemap: include the meta navigation.

Bugfixes:

* Bugfix: FAQ: syntax error in variable name.


3.3.9 (2012-05-22)
--
Improvements:

* Core: added a method to subscribe to events from within the installer.
* Profiles: added a widget that shows a login-box.
* Core: upgraded CKFinder to 2.2
* Core: upgraded CKEditor to 3.6.3

Bugfixes:

* Core: wrong application in the virtual applications, such as backend_ajax, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/297.
* Pages: fixed an issue where pages that were dropped on an empty footer-tree disappeared, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/295.


3.3.8 (2012-05-15)
--
Improvements:

* Search: don't add utm_* parameters when a search is performed.
* Core: detecting the browser language now use the weight.

Bugfixes:

* Mailmotor: the url-parameter in the pagination should be encoded.
* Location: if no markers were available an JS-error was thrown.


3.3.7 (2012-05-08)
--
Improvements:

* Core: upgraded Highcharts to 2.2.3
* Mailmotor: show subscriptions for all groups.
* Mailmotor: show unsubscriptions for all groups.

Bugfixes:

* Pages: made it possible to delete drafts.
* Blog: fixed the blog archive, which redirected to a false (or non-existent) url if the parameters were invalid


3.3.6 (2012-05-01)
--
Improvements:

* Core: upgraded jQueryUI to 1.8.20

Bugfixes:

* Pages: child pages of footer pages had the wrong type when dropped on a footer page.
* Form builder: it is now possible to use an inactive frontend language.


3.3.5 (2012-04-24)
--
Improvements:

* Core: upgraded jQueryUI to 1.8.19
* Core: SELECT 1 ... LIMIT 1 in favor of SELECT COUNT(*), more optimised queries.
* Spoon: merged changes.
* Core: create minify cache folders if they do not exist.


3.3.4 (2012-04-17)
--
Bugfixes:

* Spoon: session should be started before we can access the session.
* Mailmotor: set action and module when initializing an AJAX action.
* Form Builder: sort submissions by insert sequence. Reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/266-form builder-submissions-view-bug/


3.3.3 (2012-04-03)
--
Improvements:

* Core: added template-modifier to parse widgets.

Bugfixes:

* Spoon: Multi-checkboxes and radio buttons could have ids with spaces in them. Fixed thx to Anysurfer.
* Core: when using Fork in non-multi-language-mode the links for the internal pages weren't generated correctly, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/285-editor-adding-intern-links-error.
* Spoon: The selected element for a multiple drop-down were forgotten when the form failed.
* Core: upgraded the Akismet class, which fixes an error of double encoding, as mentioned on https://twitter.com/#!/tellyworth/status/180138255340142592.


3.3.2 (2012-03-27)
--
Improvements:

* Core: upgraded jQuery to 1.7.2
* Pages: added a timestamp after the linklist-file so it won't get cached by the browser.
* Core: upgraded Highcharts to 2.2.1

Bugfixes:

* Core: don't shorten hex codes surrounded by quotes in minifier; in some cases IE can't handle short hex codes.
* Location: invalid usage of getModuleSettings()
* Core: Fix issue with multiple editor warnings.


3.3.1 (2012-03-13)
--
Improvements:

* Profiles: made it possible to add a profile.

Bugfixes:

* Core: Escape the input on ajax searches.
* Core: Escaped weird input in Locale-module.


3.3.0 (2012-03-06)
--
Improvements:

* Core: added some JS to automatically add a .filled class on all form fields that are being filled out.
* Core: only images that are smaller then 5kb will be included in the CSS-file.
* Core: save cookies httponly by default & automatically secure when browsing over https.
* Core: make cache-files inaccessible over http.
* Locale: improved existing translations.
* Locale: added translations for Spanish (by Yéred Zabdiel)
* Locale: added translations for Swedish (by Erik Holmquist - http://www.holmquist.de & Peter Mayertz - http://www.mayertz.se)
* Locale: added translations for Ukrainian (by Манжела Борис)
* Locale: added translations for Lithuanian (by Rolanda Naujasdizainas - http://www.naujasdizainas.lt)
* Location: revised Location-module, added some functionality.
* Pages: added widget for previous/parent/next navigation.
* Users: show user account statistics on dashboard (last login, last failed login, last password change & password strength).
* API: Added a client to the API, useful for general API development and working with third parties.
* Core: sharing widget now uses the latest LinkedIn sharing button.
* Core: sharing widget now also supports Google Plus.
* Search: removed deprecated addIndex/editIndex from BackendSearchModel.

Bugfixes:

* Core: module validation did not take the special core module in account.
* Core: JS module validation has been fixed.
* Core: fix XSS vulnerability on ajax searches.

3.2.7 (2012-02-28)
--
Improvements:

* Core: upgraded jQueryUI to 1.8.18
* Core: fixed XSS vulnerabilities.
* Core: refactored code to unify setting/getting module/action and added additional checks for validity.

Bugfixes:

* Core: fixed issue where media-embed would always embed the media in the last editor, not the selected one.
* Tags: fixed call to deprecated (removed) method, which caused "related" widget to malfunction.


3.2.6 (2012-02-21)
--
Improvements:

* Core: it is now possible to use positions inside modules' templates.
* Backend: first page after login will always be dashboard (if allowed).
* Core: application specific config files are now optional.

Bugfixes:

* Pages: blocks in fallback positions are now drag-and-droppable again.
* Core: minifier now also works on PHP <5.2.2.
* Core: fixed bug in minifier where @import url("xxx") would fail.


3.2.5 (2012-02-14)
--
Bugfixes:

* Core: fixed LFI vulnerability.
* Core: you can now override the template for sub navigation and the navigation: {$var|getnavigation:'page':{$page.id}:2:null:null:'/Core/Layout/Templates/subnavigation.tpl'}
* Extensions: installing a pre-uploaded theme from the themes overview now installs the selected theme instead of the last theme.
* Mailmotor: fix CSV address imports.
* Pages: include footer/meta subpages in the linkedlist.


3.2.4 (2012-02-07)
--
Improvements:

* Core: integrated new CSS minifier (combine imports, import images to inline data URIs, shorten hex colors, strip whitespace, strip comments)
* Core: integrated new JS minifier (strip whitespace, strip comments)
* Core: replaced both different frontend & backend minifiers, by this new minifier.
* Share: Twitter now uses title instead of description + language attribute added
* Core: When a user doesn't have sufficient rights to access a page, he will now be redirected with the proper error code (307).
* Extensions: Modules with warnings will now be greyed out so they can be spotted easily.

Bugfixes:

* Location: fixed a javascript error with jquery.
* Location: fix vertical scrollbar inside info window.
* CSS: fixed Safari bug for DataGrid in Tabs #212
* Pages: subpages in the footer are now visible in the backend.
* Pages: default template wasn't used when adding a page.


3.2.3 (2012-01-31)
--
Improvements:

* Core: added a property 'hideHelpTxt' to the BackendFormImage and BackendFormFile classes to prevent the helpTxt span from appearing (handy for such form fields in a data grid for instance).
* Core: breadcrumb: added a count method.

Bugfixes:

* Core: snippets: made the languages to get the templates for dynamic.
* Blog: fixed improper redirect that caused blog archive pagination to malfunction.


3.2.2 (2012-01-24)
--
Improvements:

* Core: added an isPrice filter, also for text fields.
* Core: added the text color for the hover states of buttons.
* Core: when a data grid column has a certain column title(hidden, visible, published, active), the data grid will now automatically detect non-visible rows and mark them this way.
* Core: init Facebook for its JS SDK when an admin or app id is set.
* API: Added API::isValidRequestMethod($method) that checks if the request method of an incoming API call is valid for a given API method'.
* Analytics: Fixed the cronjobs execution time, should only run once a day.
* Blog, content blocks, pages: replaced the buttons for the use of versions or drafts by links with icons for consistency.
* Blog: API methods are now limited to their correct request methods.
* Extensions: improved the validation of the positions, as mention on http://forkcms.lighthouseapp.com/projects/61890/tickets/256 by Dieter W.
* Form builder: altered the splitchar, so "," can be used in values for drop-downs, checkboxes or radio buttons.
* Pages: editor will be larger by default.
* Search: use a saveIndex function instead of addIndex and editIndex.

Bugfixes:

* Core: module specific locale are now parsed in the templates when used in cronjobs.
* Core: Click To Edit above the editor should behave from now on.
* Core: added the options for the theme-specific editor_content.css and and screen.css that will be loaded in the editor.
* Analytics: Fixed the labels for keywords and referrers when updating through ajax.
* Extensions: Made clear in cronjob info text that cronjob execution times have to be spread on servers with multiple fork installations.
* Extensions: a notice was triggered when using invalid template syntax, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/257.
* Mailmotor: improve visibility of ckeditor in mailmotor.


3.2.1 (2012-01-17)
--
Improvements:

* Core: upgraded jQueryUI to 1.8.17
* Core: added a generic method to output CSV-files, which uses the user-settings for splitchar and line-ending.
* Core: it is now possible to set an empty string as recipient name in the mailers.
* Extensions: only modules with a valid name will be included in the list of installable modules.
* Blog: added an option for the god user to enable or disable the upload image functionality for the blog module.
* Installer: added a check for subfolders.
* All: template-options for available actions are now available for all modules and thus also prefixed with the module name.

Bugfixes:

* Core: added missing locale for ckeditor & ckfinder.
* Core: when not in debug mode the dialog-patch wasn't included in the minified JS-file.
* Pages: fixed reset previous value when editing editor block.
* Spoon: when deleting a cookie we now set the expiration date far in the past to prevent that users with an incorrect system time can still use deleted cookies.
* API: all illegal characters are now wrapped with CDATA tags.
* Blog: API calls now show the most recent version of a blog title.


3.2.0 (2012-01-10)
--
Improvements:

* Core: integrated CKEditor into Fork CMS.
* Core: added an extra check (parent has to be td.checkbox) for the row selection within tables in the backend.
* Core: added cookie containing unique visitor id.
* Core: add a class 'noSelectedState' to the table of a dataGrid to prevent the selected state to show for every row in the data grid with a checked checkbox.
* Core: added maxItems and afterAdd options for the multipleSelectbox.
* Core: added a possibility to add an extra to all pages when installing Fork CMS with the installer function addDefaultExtra. The extra will be added to all pages without this extra.
* Core: you can now add items to the search index in the installer of your module.
* Core: fixed core engine url notice in frontend/ and backend/ (Notice: Undefined offset: 1) by removing an unused $get var.
* Pages: when adding an editor field, the editor will immediately open.
* Pages: the sitemap now correctly displays subpages.
* Extensions: modules may now also include files in /library/external.
* All: actions where the user has no rights for, are no longer shown.

Bugfixes:

* Core: fixed core template override from within module action.
* Core: added #xfbml=1 to the Facebook connect URL so Facebook plugins also work when there's no Facebook app id given in the settings tab.
* ContentBlocks: Fixed a bug where a hidden content block assigned to a page would trigger a PHP Notice.
* Extensions: fixed module-warnings system.
* Extensions: fixed module upload.
* Users: Fixed a bug that was triggered when editing a user that was not the logged in user and when the logged in user was not a god user.
* Spoon: drop-down opt-group's values were reset by the array_merge function.


3.1.9 (2012-01-03)
--
Improvements:

* Core: the frontend CSS-minifier supports @import-statements from now on.
* Core: you can't select redirect-languages that aren't active.

Bugfixes:

* Blog: meta should be deleted before the items are deleted.


3.1.8 (2011-12-27)
--
Improvements:

* Core: added public methods to FrontendPage to fetch page id & page record.
* Core: split instantiation & execution of extras, allowing extras to be aware of other extras on a page.
* All: fixed a lot of <label>-tags, which improves the accessibility.
* All: added some hidden labels for form-elements that doesn't have a <label>-tag linked, which improves the accessibility.
* Authentication: don't mention which field is required separately.
* Core: no more need to use the addslashes-modifier in JS-files, it will be handled by Fork. Introduced while fixing the bug mentioned by Tristan Charbonnier on http://forkcms.lighthouseapp.com/projects/61890/tickets/249.
* Core: added a generic class that will enable you to use iCal-feeds.

Bugfixes:

* Core: confirm messages weren't working anymore, mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/251
* Extensions: when a templates was edited and an form-error was shown the added blocks weren't shown correctly again.
* Tags: related widget wasn't using the current language, patch provided on http://forkcms.lighthouseapp.com/projects/61890/tickets/243
* Tags: the url for a tag that contains spaces wasn't calculated correctly, mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/244
* Mailmotor: also replace https while linking the account
* Form builder: changing the value of the submit-button wasn't working, mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/252.
* Installer: show the warning when library/external is not readable.


3.1.7 (2011-12-20)
--
Improvements:

* Core: tableSequenceByDragAndDrop allows the module to be chosen, so sequences from other modules might be used.
* Tags: tag-pages don't have any SEO-value, so don't index them.
* Core: created multibyte-safe ucfirst variant and applied it throughout Fork CMS.

Bugfixes:

* Core: fixed XSS vulnerability (as mentioned on: http://packetstormsecurity.org/files/107815/forkcms-xss.txt)
* Core: fixed page-unload warning on IE.
* Core: it is now possible to use translations that don't exist in English.


3.1.6 (2011-12-13)
--
Improvements:

* Core: when not in debug mode non-existing files or faulty urls shouldn't trigger an exception but a 404.
* Core: added an getModules method to FrontendModule, analog to the backend method.
* Core: the direct actions are no longer shown in the navigation.
* Core: don't add a timestamp to the urls of well known libraries in the backend.
* Core: automagic canonical-urls.
* Core: added a new modifier stripnewlines which will remove all newlines in a string, so JS can handle it.
* Core: added schema.org properties in the default HTML and in the Triton-theme.
* Locale: added some missing locale, see http://forkcms.lighthouseapp.com/projects/61890/tickets/237
* Locale: the missing items are now sorted by application, type, module and name.
* Locale: added translations for Spanish (by Alberto Aguayo - http://www.bikumo.com)
* Location: rewrote most of the JS, because the map wasn't showing the markers correctly, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/238

Bugfixes:

* Pages: default blocks now apply correctly on new pages.
* Pages: removed extras still linked to page now no longer trigger an error.
* Core: settings exclude & checked values on setMassActionCheckboxes now works again.
* Form builder: fixed a typo, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/239.
* Core: when adding a JS-file with a ? in it the timestamp was appended with a ?.
* Locale: improved translations for German (by Philipp Kruft - http://www.novacore.de)


3.1.5 (2011-12-06)
--
Bugfixes:

* Analytics: when refreshing the traffic sources a parse-error was thrown, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/231


3.1.4 (2011-11-29)
--
Improvements:

* Core: upgraded jQuery to 1.7.1
* Core: upgraded jQuery Tools to 1.2.6
* Core: direct action pages get prefilled meta information again.
* Users: when adding a user and there is only one group it will be checked by default.

Bugfixes:

* Profiles: display name was not being urlized.
* Tags: it is no longer impossible to fetch related items with the same id as your source item.
* Core: fixed js issue in triton.
* Core: fixed a typo.
* Extensions: when using spaces in the format-part of the template XML, the templates weren't build correctly.


3.1.3 (2011-11-22)
--
Improvements:

* Added the possibility to easily adjust detailed page settings when you are a god user.
* SVN folders will now be skipped when running the remove_cache script.

Bugfixes:

* Core: fixed an issue with the checkboxTextfieldCombo function.
* Core: fixed minified media queries in the backend CSS manually, the minify script itself has to be adjusted though.
* Core: fixed inputCheckbox positioning inside data grids.
* Core: fixed the row selected state in the data grid when the selectAll checkbox was clicked.
* Core: fixed the layout dataFilter function since it scoped the wrong, lowercased class of the dataFilter.
* Extensions: prevented PHP warnings when no info.xml is available.
* Core: fixed an issue with drag and drop in the backend.
* Locale: importing other languages then EN is possible again.
* Core: fixed an issue with the user-drop-down.
* Form builder: fixed an issues with the default error messages.
* Blog: deleting a blog image caused a SQL error.
* Core: upgraded the YUI-compressor to 2.4.7, see https://github.com/yui/yuicompressor/blob/master/doc/CHANGELOG.
* Core: javascript error fixed when no href is provided in the share widget.
* Core: fixed confirmation-dialog, wasn't closing when the cancel-button was clicked.
* Tools: frontend and backend globals were not deleted when running prepare_for_reinstall.


3.1.2 (2011-11-15)
--
Bugfixes:

* Core: fixed an issue with items that used .live().
* Pages: fixed an issue with dynamically added elements using .data().


3.1.1 (2011-11-15)
--
Improvements:

* Locale: refactored importXML method to also be used by installer (rather than 2 separate "different yet the same" functions).
* Extensions: add cronjobs info to info.xml, informational al well as for checking whether all cronjobs are set.
* Core: upgraded Highcharts to 2.1.8.
* Core: major improvements (code-styling, spelling, performance, ...) for JS, credits to Thomas.
* Core: upgraded jQuery to 1.7
* Installer: when the form in step 6 (where the actual install happens) is submitted the button will be replaced with a spinner to indicate the installer is running.
* Analytics: added a warning when trying to link a profile when no profile was selected.
* Blog: when there are 2 or more categories with at least one item in it, the category will be added in the breadcrumb.

Bugfixes:

* Editing tags wasn't working because of an error in the SQL-statement in the FAQ-module.
* Missing label, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/212.
* Pages: closing the dialog did not discard the content correctly.
* Core: autocomplete on tags wasn't working due the change of the AJAX-calls.


3.1.0 (2011-11-08)
--
Improvements:

* Core: Upgraded TinyMCE to 3.4.7
* Core: TinyMCE now includes all languages that are possible in the interface-language-drop-down.
* Core: the keys when asking for a locale item now get camelcased so you can add enum values f.e. when using them in a data grid.
* Form builder: made it possible to add multiple receivers.
* Pages: added a widget that shows the subpages as blocks with their title and meta description.

Bugfixes:

* Core: when requesting the meta-navigation while there are no items an exceptions was thrown, mentioned by Niels on http://forkcms.lighthouseapp.com/projects/61890/tickets/195.
* Core: when editing non-active languages the files parsed through javascript.php were using the default language, as pointed out by Simon on http://forkcms.lighthouseapp.com/projects/61890/tickets/200.
* Core: fix default module, action, language in JS - was messed up on dashboard.
* Core: fix issue in template compiler; nested iterations where child ends in name of parent, did not work.
* Core: removed the guessing of the library path in the installer. When Spoon can't be located a textfield will be shown wherein you can enter the path to Spoon.
* Core: fixed issue when displaying empty pages without blocks linked.
* ContentBlocks: fixed a database exception when deleting content blocks.
* Extensions: fixed typo, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/207 by Bart.
* Extensions: editing a template without default-data was triggering a notices, as mentioned by Bart on http://forkcms.lighthouseapp.com/projects/61890/tickets/204.
* Extensions: confirm-messages through pure Javascript don't support sprintf through the template-engine, see http://forkcms.lighthouseapp.com/projects/61890/tickets/203.
* Extensions: ignore hidden files when validating the uploaded zip-files, see http://forkcms.lighthouseapp.com/projects/61890/tickets/208.
* Form builder: when a field isn't required, but should be validated as an e-mail address it was forced to be filled in.
* Form builder: the language wasn't saved correctly into the extras after editing a form, so it was shown for all languages, as mentioned by Simon on http://forkcms.lighthouseapp.com/projects/61890/tickets/201.
* Location: invalid item was used in the template, and the JS should only be executed after jQuery is loaded, as mentioned by Floris on http://forkcms.lighthouseapp.com/projects/61890/tickets/205.
* Pages: class name for sitemap was wrong.
* Pages: navigation now contains valid depth-key in template.
* Tags: inline editing wasn't working anymore due the new way of using AJAX.
* Tools: improved whitespace-check in codesniffer.
* Faq: fixed collation of table faq_feedback.


3.0.0 (2011-11-01)
--
Improvements:

* Core: completely re-invented the blocks system; it's now position-driven.
* Core: introduce the concept of positions that can contain an arbitrary number of blocks.
* Pages: merged tabs "Content" & "Template" to present a more straightforward UI.
* Pages: added ability to order blocks on a page.
* Pages: created fallback-system for blocks that were assigned to no-longer-existing positions.
* Pages: added the possibility to show/hide a block.
* Pages: edit HTML content in TinyMCE in a dialog.
* Core: updated installer.
* Core: updated template creation in backend.
* Core: updated theme Triton to be position-based.
* Pages: added the possibility to either completely overwrite or re-use existing blocks when updating a template.
* Core: removed has_extra and extra_ids from pages database and replaced it with joins resulting in the same result but based upon real data (rather than just relying on the existing scripts.)
* Installer: added 'getTemplateId' function to easily fetch a template id.
* Installer: added 'warnings' to warn for less optimal systems but allow installation anyway.
* Installer: added improved test for mod_rewrite (will produce warning if not enabled.)
* Installer: refactored code: every step now double checks all previous steps and redirects back on error.
* Core: updated folder structure to prevent installation issues with folders needing to be outside the document root.
* Core: removed "markup" folder, this is now available at http://www.fork-cms.com/markup.
* Core: allow for non-standard characters to be used in urls.
* Core: validate slugs that are being added with javascript whilst typing the title using meta-class.
* Core: updated default favicon.
* Pages: updated pages getNavigation; the 'includeChildren' parameter was useless.
* Core: refactored javascript ajax-calls.
* Installer: refactored installation of dashboard widgets.
* Analytics: dashboard widgets are now added for all users upon installation.
* Profiles: refactored action names to better represent their purpose.
* Extensions: uploaded modules can be installed.
* Extensions: uploaded themes can be installed.
* Extensions: it is now possible to install modules via ZIP upload in the CMS.
* Extensions: it is now possible to install themes via ZIP upload in the CMS.
* Core: the active state of modules has been stripped. This is no longer useful.
* Core: new coding standards have been applied.
* Locale: added translations for Chinese (by Millie Lin - http://www.witmin.com)
* Locale: added translations for French (by Matthias Budde - http://www.flocoon.com & Jeremy Swinnen - http://blog.stratos42.com)
* Locale: added translations for German (by Philipp Kruft - http://www.novacore.de)
* Locale: added translations for Hungarian (by Bota David - http://kukac7.hu)
* Locale: added translations for Italian (by NebuLab - http://nebulab.it)
* Locale: added translations for Russian (by Медведев Илья - http://iam-medvedev.ru)
* Locale: added partial translations for Turkish (by Serkan Yildiz - http://twitter.com/#!/GeekOfWeb)
* Locale: added partial translations for Polish (by Pawel Frankowski - http://www.blog.elimu.pl & Konrad Confue Przydział - http://confue.xaa.pl)
* Blog: the blog module now standard has an image field.


2.6.13 (2011-10-18)
--
Improvements:

* Locale: make it possible to browse translations for all modules at once.
* Mailmotor: email address can't be edited; change code to reflect this.
* Core: Facebook open Graph-tags will now be parsed when an app OR admin-id is configured.
* Core: added a share-widget, see http://www.fork-cms.com/knowledge-base/detail/using-the-share-widgetmenu.

Bugfixes:

* Content Blocks: some backend functions didn't take into account the current working language.
* Content Blocks: exclude invalid templates.
* Installer: some very specific Apache-version will prepend the Apache-variables with REDIRECT_.
* Pages: when adding more then 1 module to a page you will get a nice error message instead of a PHP error.


2.6.12 (2011-10-11)
--
Improvements:

* Core: removed empty method.
* Core: detect if .htaccess is available and mod_rewrite enabled in the installer.
* Core: when adding a file field it is now possible to easily show a label with the available extensions.


2.6.11 (2011-10-04)
--
Improvements:

* Core: Made the parent_id available in the template.
* Core: Upgraded TinyMCE to 3.4.6
* Core: Made the Facebook integration work with the signed requests.

Bugfixes:

* Core: re-added some missing locale into the image-manager, see: http://forkcms.lighthouseapp.com/projects/61890/tickets/185-268-moxicode-unassigned-literals.
* Core: fixed some errors in the api-methods for blog.
* Core: fixed a bug where updating a page template tried to input data in a non-existing database column.
* Core: fixed a typo in the dutch disclaimer, see: http://forkcms.lighthouseapp.com/projects/61890/tickets/190.


2.6.10 (2011-09-27)
--
Improvements:

* Search: IP address is no longer shown in statistics.
* Core: Improved config to let TinyMCE cleanup Internet Explorer HTML.
* Search: Search won't show the 404 page anymore, see: http://forkcms.lighthouseapp.com/projects/61890/tickets/186-268-search-finds-404-page.

Bugfixes:

* Groups: when no bundled actions were available a PHP notice was thrown.
* Dashboard: validate if a position is already taken.
* Pages: sort sequences after checking its existence.


2.6.9 (2011-09-20)
--
Improvements:

* Core: Upgraded jQuery to 1.6.4.
* Core: When an image/file field is added in the backend the max_upload_size is added as a help-message, see: http://forum.fork-cms.com/discussions/general/59-display-max-upload-size-backend.
* Core: Added an api-method to remove an apple-device token.
* Core: Emails are now send base64 encoded. This to prevent that line breaks, which are added when the max text line length is reached, corrupt the content.
* Blog: Added an api-method to grab a single comment.
* Blog: When calling blog.comments.UpdateStatus you can pass multiple ids by separating them with a ,.
* Tags: Overview is now sorted alphabetically.

Bugfixes:

* Blog: Fixed a bug in the blog module where it called an non-existing FrontendTag-function.


2.6.8 (2011-09-13)
--
Improvements:

* Core: TinyMCE link-list is now sorted according the pages-tree, as requested by Frederik (http://forum.fork-cms.com/discussions/feature-requests/11-tinymce-linklist-sort).
* Core: Mails from form builder will contain the site title instead of Fork CMS.
* Core: Updated the schema.

Bugfixes:

* Blog: deleting a draft no longer triggers an error.
* Blog: fix deletion of category: check for blog-posts in category did not check blog status.
* Groups: permission management now works correctly in Chrome.


2.6.7 (2011-09-09)
--
Bugfixes:

* Install triggered an error "Headers already sent".

Improvements:

* Core: Upgraded TinyMCE to 3.4.5 - fixed Opera issues with editor.
* Core: Updated JS utils.urlise to better reflect the SpoonFilter::urlise (. should also convert to dash)
* Core: Shorter GA-tracking code


2.6.6 (2011-09-06)
--
Bugfixes:

* Facebook-class: fixed oAuth-calls.
* Autoloader was replacing too much, when using the module name inside an action (eg: mass_files_action in the module files).

Improvements:

* Core: upgraded jQuery to 1.6.3.
* Core: added two method (getDate & getTime) as BackendDataGrid-functions, as requested by Frederik (see: http://forum.fork-cms.com/discussions/general/48-shortdate-for-formatting-dat-in-datagrid).


2.6.5 (2011-08-30)
--
Improvements:

* Core: backend navigation is now dynamically generated. Module installers can set their navigation tree.
* Core: improved default-filtering in locale. From now on frontend and all types are default.
* Core: return id of inserted mail.
* Pages: sitemap page will now also display child pages.


2.6.4 (2011-08-23)
--
Bugfixes:

* Location: it is now possible to use multi-line content inside the marker.
* Core: overwriting javascript-files in a theme now works fine.

Improvements:

* Core: upgraded jQueryUI to 1.8.16.


2.6.3 (2011-08-16)
--
Bugfixes:

* Api: when the response isn't an array notices where thrown.
* Locale: analyse now correctly handles dynamic translations.
* Core: local file inclusion check was not MS Windows-proof, fixed now.
* Core: the metaCustom was never parsed.
* Pages: when there are no footer-pages an notice was triggered (as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/176).
* Pages: When moving a page the correct page is now checked for allow_children

Improvements:

* Core: added a modifier to camelcase strings.
* Core: when adding new default blocks to an existing template, update all corresponding pages that have no content in those blocks to the new default.
* Core: when Akismet can't tell us if a comment is spam, we mark it as an item in moderation.
* Core: added functionality to set a callback after an item is saved with inline editing.
* Pages: internal redirect can have children from now on.
* Pages: added an experimental copy-action.
* Locale: highlight empty items in the overview.


2.6.2 (2011-08-09)
--
Bugfixes:

* Core: template custom was not being parsed inside blocks.

Improvements:

* Core: upgraded jQueryUI to 1.8.15.
* Core: added a way to read a cookie through JS.
* Core: Upgraded TinyMCE to 3.4.4


2.6.1 (2011-08-02)
--
Bugfixes:

* Search: search page was installed twice.
* Core: when in debug-mode the confirmation for leaving the page is disabled.
* Core: the check that decided to show the confirmation-message wasn't handling empty strings very well.
* Core: fixed some JS-errors

Improvements:

* Core: added utils.string.html5(), when you pass a HTML5-chunk it will be converted so IE will render it correctly (based on innerShiv).


2.6.0 (2011-07-26)
--
Bugfixes:

* Blog: Tags are now correctly fetched and displayed.
* Blog: Comments-action was broken due an invalid call on $this in a static method.
* Installer: Setting the library-path was using an array instead of the first item in that array.

Improvements:

* Core: Items marked as direct action won't show up in page-title, breadcrumb, meta, ...
* Core: Better handling of meta-information. Each item will be unique, Some new methods are introduced (addLink, addMetaData, addMetaDescription, addMetaKeywords, addOpenGraphData), they replace: setMeta*.
* Core: Added an SEO-item in the advanced-settings-section. For now only noodp and noydir are implemented.
* Core: Added advanced SEO-settings in the SEO-tab (index,follow).
* Core: Added a setting to use no-follow on links inside user-comments.
* Core: If Google Analytics is available, all outgoing links will be tracked by event-tracking.
* Core: When Google Analytics is linked, and the tracking-code isn't found in the header/footer-HTML it will be added.


2.5.2 (2011-07-19)
--
Bugfixes:

* Core: Event logging now uses absolute paths to prevent usage of undefined constants.


2.5.1 (2011-07-19)
--
Bugfixes:

* Installer: Installer now uses `is_writable` to check if a folder is writable, see http://forkcms.lighthouseapp.com/projects/61890/tickets/172.
* Spoon: On rare occasions iconv would trow an error that it can't convert strings.
* Core: js.php could be misused.


2.5.0 (2011-07-12)
--
Bugfixes:

* Pages: Don't show hidden extras in the widget- and block-drop-downs.
* Pages: hidden modules_extras don't get shown in the template anymore.
* Pages: when editing a page with a external redirect there was an error because of the disabled field, fixed the JS, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/169.

Improvements

* Core: Removed code to initialize the session, this is just useless and prevents caching-proxies to work by default.
* Core: upgraded TinyMCE to 3.4.3.2
* Core: Pub/sub-system, see: http://www.fork-cms.com/blog/detail/pubsub-in-fork


2.4.2 (2011-07-05)
--
Improvements:

* Core: Facebook doesn't provide an API-key anymore, so code is altered to reflect this.
* Core: siteHTMLFooter should be append after the JS-files.
* Core: implemented social-tracking for GA, will only be executed if Google Analytics is used, and facebook or twitter are integrated.
* Core: upgraded jQuery to 1.6.2 and jQueryUI to 1.8.14.


2.4.1 (2011-06-28)
--
Bugfixes:

* Blog: blogger import script now downloads the images correctly.


2.4.0 (2011-06-21)
--
Bugfixes:

* ContentBlocks: template wasn't selected when editing the block.

Improvements:

* Profiles: added profiles module to handle on-site (frontend) profiles.
* Groups: added groups module to handle backend user privileges.
* Locale: added quick-edit.
* Core: extras (blocks or widgets) now simulate their own scope concerning templates.
* Core: no more language if there is just one language enabled.
* Core: handling of meta/links tags is now down through code, therefor you can overrule existing values.
* Core: removed deprecated methods.


2.3.1 (2011-06-14)
--
Bugfixes:

* Form builder: fix jquery error causing form builder to malfunction
* Proper implementation of .prop().
* Analyse-action was using invalid arguments for SpoonFilter::toCamelCase().

2.3.0 (2011-06-07)
--
Bugfixes:

* Core: when the meta fields are disabled we don't have any values in the POST. When an error occurs in the other fields of the form the meta-fields would be cleared. As reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/164.
* Pages: moving pages for a non-active language failed, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/163.

Improvements:

* Core: Upgraded to jQuery 1.6.1
* Core: Upgraded to jQuery UI 1.8.13
* Core: Upgraded TinyMCE to 3.4.2

2.2.0 (2011-06-01)
--
Bugfixes:

* Bugfix: inline editing for blog-categories wasn't working anymore, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/132.
* Bugfix: when an error was thrown while inline editing, the element wasn't destroyed.
* Bugfix: title of blogpost had inline-editing enabled while this isn't implemented.
* Bugfix: options aren't visible elements for webkit-browsers. So submitting the first parent-form was failing in mass-actions.
* Bugfix: improve "incomplete" (autocomplete) searching for multiple words (only the last word should be considered incomplete.)
* Bugfix: removed empty widgets, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/150.
* Bugfix: hover-event wasn't unbind correctly when sorting the widgets was done.
* Bugfix: importing addresses into the mailmotor was borked, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/143.
* Bugfix: focusFirst was focusing on an element on hidden tabs, as reported on http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/153.
* Bugfix: click on tab wasn't working decent in IE, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/154.
* Bugfix: page-revisions were interfering with blog-revisions, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/151.
* Bugfix: theme-css is now loaded again into TinyMCE.
* Bugfix: only remove language from query string when we have multiple languages.
* Bugfix: backend interface language was not set according to our installer selection.
* Bugfix: added the correct anchor on the blog comment-form, fixes: http://forkcms.lighthouseapp.com/projects/61890/tickets/159.
* Bugfix: create category dialog in blog-module wasn't working when there weren't no categories, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/160
* Bugfix: date fields weren't populated with the date that was set, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/161.

Improvements:

* Core: when using date fields with till, from, range set, it will be validated according the type.
* Core: changed theme's folder layout to match codebase folder layout - folder 'layout/' should be included in theme;.
* Core: changed addJavascript function to addJS (consistency with addCSS + less typing.)
* Core: added class FrontendTheme with functions pertaining to themes. Bundled functionality to fetch a file's theme path to this class.
* Core: added template modifier 'getPath', to fetch the desired path to a file (theme file if available, core file otherwise.)
* Core: no more need to enter absolute path to core or theme template in an include (still possible though); template compiler will use theme file if available, core file otherwise.
* Core: removed scratch theme. Triton is now the default theme.
* Core: templates are now linked to a theme.
* Core: only show templates belonging to a specific selected theme.
* Core: theme switch will automatically link pages to templates of the new theme.
* Core: when a new template with less blocks is selected for a page, the redundant blocks' content will be kept.
* Core: blocks data does not get lost when switching template/theme.
* Core: content blocks can now be linked to a content block-template.
* Core: locales analyse-tool will check only the active modules from now on.
* Core: added a jQuery-plugin to implement a passwordGenerator.
* Core: added the possibility to add attachments to the frontend/backend mailers.
* Core: when calling *Form::getTemplateExample() an example that reflect the correct markup for that application will be returned.
* Core: default jQuery-theme is now Aristo (see: http://taitems.tumblr.com/post/482577430/introducing-aristo-a-jquery-ui-theme).
* Core: made date picker-stuff available in the frontend.
* Core: made it possible to change the amount of blocks for templates that are in use. When blocks are removed, the content will no longer be shown; when blocks are added, the defaults will be pushed to the existing pages.
* Blog: creating categories can now be done without leaving the add/edit screen.
* Blog: changes to improve the usability: no more default category, users are forced to select a category if there are multiple categories.
* Blog: when filtered on a category and clicked on link to add a post the category will be prefilled.
* Blog: in the drop-down to filter on a category the count is now included.
* Blog: when canceling adding a new category the previous selected one will be reselected, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/147
* Pages: Redirecting to child pages (if there is no content) will now use 301-code.
* Pages: implemented drafts, similar to Blog.
* Pages: when changing templates the textual-content isn't deleted anymore.
* Locale: you can now import/export locale from/to xml. The installers also use xmls.
* Locale: export for missing locale.
* Locale: remove deprecated insertLocale function.
* Locale: created an incredibly nasty hotfix for some deprecated PHP functionality.
* Mailmotor: added extra validation (reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/137).
* Mailmotor: added extra validation for adding address(es).
* Mailmotor: adding multiple addresses now uses the multipleTextbox-functionality.
* Installer: refactored pages installation.
* Installer: split up step languages & modules into 2 steps; moved db step behind those.
* Installer: ask for backend interface languages separate from frontend languages.

2.1.0 (2011-03-14)
--
* IE-stylesheets aren't loaded by default, this is the task of the slices (as requested/indicated by Yoni)
* Force forms to use UTF-8
* Blog categories now use the meta-object
* Cronjobs can now be triggered from the CLI, as requested on http://forkcms.lighthouseapp.com/projects/61890/tickets/120
* Core: improvements for number formatting
* Tools: scripts are now using find
* Bugfix: Disabled the image-managers context-menu because there are still issues (according to the TinyMCE developers :s)
* Bugfix: $_GET-parameters were double urldecode, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/82
* Bugfix: navigation used to give notices with hidden/excluded pages
* Bugfix: autoloader path to FrontendBaseAjaxAction was incorrect
* Bugfix: setting a language for an ajax-call on non-multi-language sites wat a bit * ehm * fubar
* Bugfix: when deleting a content_block, the HTML-field for the block should be set to an empty string (thx to Frederik Heyninck)
* Core: renamed addCSSFile to addCSS, to reflect the backend (thx to Frederik Heyninck)
* Bugfix: loading of classes in getWarnings should use SpoonFilter::toCamelCase instead of ucfirst (thx to Frederik Heyninck)
* Pages: added the getForTags-method
* Core: added JS to enable placeholder-behaviour in browsers that doesn't support placeholders
* Core: made it possible for cronjobs to use BackendMailer
* Core: made it possible to use setColumnConfirm on other columns that haven't a link as value
* Core: made it possible to highlight elements via a GET parameter
* Bugfix: tagBox and multipleTextbox now work as intended when typing the splitchar
* Bugfix: multipleTextbox no longer blocks the form submit in specific cases
* Bugfix: widgets now also use theme templates (if available)
* Core: using data-attribute instead of rel
* Blog: reimplemented drafts
* Bugfix: recalculate num_comments so the new revision has the correct count
* Core: fixed a lot of code to reflect the styleguide
* Testimonials: made the module language-dependant (as it should be)
* Bugfix: SpoonFileCSV was triggering a warning when no exclude-columns were provided
* Core: backendMailer will remove tags from the subject
* Core: added the password generator into the frontend
* Bugfix: selecting a template in teh mailmotor wasn't working in IE, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/99
* Bugfix: non-existing items were included in the getAll-method
* Core: non-absolute urls are replaced when using Backend/Frontend-mailer
* Bugfix: multipleSelectbox is now working as it was intended
* Core: it is now possible to tell the code not to add a timestamp on the url for CSS/JS
* Bugfix: changing the working language was redirecting to dashboard instead of the module, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/91
* Bugfix: editing a tag was calling an non existing method
* Content blocks: make sure you add an extra column "extra_id"
* Bugfix: Blog was using the revision-id instead of the id for retrieving tags
* Core: Facebook should be add in the footer instead of the header because Facebook sucks
* Core: improvement for pagination (should fix http://forkcms.lighthouseapp.com/projects/61890/tickets/88)
* Blog: it is now possible to remove all spam at once
* Pages: extra validation, so home can't have any blocks
* Pages: improvement for changing extras, as requested on http://forkcms.lighthouseapp.com/projects/61890/tickets/77
* Bugfix: mailmotor was reporting empty groups when adding a newsletter, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/111
* Bugfix: minifying the CSS files should replace path to images, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/108
* Core: dashboard can now be customized by the user
* Tools: added a script to generate statistics for the codebase
* Core: isCached now always returns false when SPOON_DEBUG is true
* FormBuilder: added the form builder module.
* Mailmotor: now works with CampaignMonitor API v3
* Mailmotor: reworked settings; You can now unlink accounts and choose an existing client to link with.
* Mailmotor: thanks to the reworked import functionality in the CM API v3, the address-import should go a lot faster.
* Mailmotor: you can now pick your own default groups after importing data of an existing client.
* Core: Integrated Facebook in the frontend, when an Facebook-app is configured, a facebook-instance will be available in the reference (Spoon::getObjectReference('facebook')). When the user has granted the correct permission you will be able to communicate with Facebook as that user.
* Bugfix: changing a page template to a template with more blocks caused an exception.
* Pages: use the new Triton theme when installing a new Fork with example data.
* Pages: hidden pages don't have the view-button anymore, as requested on http://forkcms.lighthouseapp.com/projects/61890/tickets/123
* Bugfix: Meta-navigation subpages not shown in backend, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/129
* Dashboard: Fixed issue with dashboard that wasn't scalling anymore, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/130
* When FB-admin-ids are given, the facebook-statistics-tag is added on all pages

2.0.2 (2010-11-24)
--
* Added .gitignore files again
* Upgraded jQuery and jQuery UI to latest version
* Upgrades Spoon to reflect the latest changes on their repo (extra methods in SpoonLocale)
* Core: added a modifier to format a string as currency (frontend)
* Core: added a modifier to format a string as a float (backend)
* Blog: when the rss_image.png exists in your theme, it will automatically be used in your rss feed.
	* moved the code for default RSS-image into FrontendRSS
* Pages: added sorting for extras in drop-downs
* Bugfix: extras weren't populated when the template was changed
* Bugfix: URL was changed when moved if the page was an direct sub-action as reported in http://forkcms.lighthouseapp.com/projects/61890/tickets/29-url-gets-changed-when-dragging-a-page-with-isaction-checked
* Bugfix: contact module has no backend, so no button should appear in the pages-module, as reported on http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/34-edit-module-contact-no-config-file-found#ticket-34-3
* Core: password strength-meter should report passwords with less then 4 characters as weak, as reported on http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/33-installer-step5-password-weakness-indicator#ticket-33-3
* Core: added a script that enables us to restore the directory/file-structure like Fork wasn't installed before
* Tags: added a tag cloud-widget
* Core: added an extra modifier to grab page related info (getpageinfo)
* Bugfix: mass checkbox and mass drop-down behaviour now function as intended
* Bugfix: z-index of modal and resize-handle, as reported in http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/37-design-ui-bug-mailmotor#ticket-37-4
* Mailmotor: corrected some labels
* Mailmotor: added a warning if the module isn't linked (so preview won't trigger a 404)
* Bugfix: FrontendRSS now handles an encoded RSS title as intended
* Core: added some labels
* Core: added an new modifier "formatfloat"
* Contact: added author in the subject of the mails, so spam can be detected without checking the email, and Mail.app won't mess up threads
* Tags: fixed some todo's, fixed some stupid code, wrote markup that can be used in a real life project for the default template
* Locale: Implemented some remarks on Locale-module, see http://www.fork-cms.com/blog/detail/the-translations-module
* Styled analyze function of locale module
* Added docs page on installing
* Core: added a modifier to strip the tags from a string (frontend)
* Bugfix: FrontendRSS, special chars should de decoded (thx to Unrated)
* Blog: added a method to get related
* Bugfix: fixed the config of the file- and image-manager so they can handle symlinks. (and deployment)
* TinyMCE now get a TinyActive class when active. Fixed Fork tinyMCE skin bugs including wide scrollbar. (always wrap a tinyMCE in `<div class="options">` or `<div class="optionsRTE">`)

2.0.1 (2010-11-03)
--
* added correct .gitignore-files and ignored .git
* fixed some stuff so app is ready for deployment with Capistrano
* added a script to minify stuff from backend (and put in correct folder)
* core: files with extension jpeg are allowed from now on in TinyMCE image-manager.
* core: installer required javascript to be enabled, so added a check.
* core: installer will clear previous cached data
* core: database-port is now configurable
* core: minor improvements for user-interface.
* core: improved BackendMailer
* core: fixed some labels
* core: when a template used by the mailer exist in the theme it will overrule the default
* core: Better styling for drag/drop tables + added success message after reorder
* core: upgraded CSSToInlineStyles to the latest version
* core: added a method to build a backend URL from the frontend
* blog: fixed installer (comments, rights, ...)
* blog: added a feed on each article with the comments for that article
* blog: added a feed with all comments (on all articles)
* blog: added notification on new comments (settings in backend)
* pages: Made it possible to move stuff from tree into an empty meta-navigation
* mailmotor: preview is now sent with BackendMailer.
* mailmotor: utf8 instead of latin1.
* mailmotor: synced TinyMCE "look and feel" from core
* bugfix: tinyMCE stripped the embed-tag
* bugfix: comment_count on blog articles ignored the archived/draft status
* bugfix: spam comments couldn't be removed.
* bugfix: generating an URL for a block didn't passed the language in the recursive part.
* bugfix: correct detection of sitemap-page
* bugfix: fixed some calls to BackendPagesModel::buildCache() (language should be passed)
* bugfix: deleting a blog post resulted in an error.
* bugfix: pages disappear when moving in separate pages
* bugfix: when deleting a blog-category blogpost were not moved into the default category
* bugfix: CURLOPT_xxx options should be integer/constants instead of strings
* bugfix: limited index length for table modules_settings to overcome SQL error 'Specified key was too long; max key length is 1000 bytes'
* bugfix: date picker days of week are now correct
* bugfix: fixed UTF-8 issue in contact-module, remember we're using UTF-8, so mails should have teh correct meta-tag
* bugfix: fixed issue with addURLParameters-method, which fucked up URLs with a hash in them.
* bugfix: fixed comment-count on overview.
* bugfix: when a module was linked, and the block was changed, you couldn't select module again.

2.0.0 (2010-10-11)
-----
None
