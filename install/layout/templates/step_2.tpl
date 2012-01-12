{$head}

<form action="index.php" method="get" id="step2" class="forkForms submitWithLink">
	<div>
		<input type="hidden" name="step" value="2" />
		<div class="horizontal">
			{option:error}
			<div>
				<div class="formMessage errorMessage">
					<p>Your server doesn't meet the minimum requirements to run Fork CMS.</p>
				</div>
			</div>
			{/option:error}
			{option:!error}
			{option:warning}
			<div>
				<div class="formMessage warningMessage">
					<p>Your server might not run Fork CMS properly.</p>
				</div>
			</div>
			{/option:warning}
			{/option:!error}
			<div id="requirementsInformation">
				<h3><span class="{$phpVersion}">{$phpVersion}</span> PHP version</h3>
				<p>We require at least PHP 5.2</p>

				<h3><span class="{$subfolder}">{$subfolder}</span> Fork CMS can't be installed in subfolders</h3>

				<h3>PHP Extensions</h3>
				<h4><span class="{$extensionCURL}">{$extensionCURL}</span> cURL</h4>
				<p>
					cURL is a library that allows you to connect and communicate to many different type of servers. More information
					can be found on: <a href="http://php.net/curl">http://php.net/curl</a>.
				</p>

				<h4><span class="{$extensionLibXML}">{$extensionLibXML}</span> libxml</h4>
				<p>
					libxml is a software library for parsing XML documents. More information
					can be found on: <a href="http://php.net/libxml">http://php.net/libxml</a>.
				</p>

				<h4><span class="{$extensionDOM}">{$extensionDOM}</span> DOM</h4>
				<p>
					The DOM extension allows you to operate on XML documents through the DOM API with PHP 5. More information
					can be found on: <a href="http://php.net/dom">http://php.net/dom</a>.
				</p>

				<h4><span class="{$extensionSimpleXML}">{$extensionSimpleXML}</span> SimpleXML</h4>
				<p>
					The SimpleXML extension provides a very simple and easily usable toolset to convert XML to an object that can be
					processed with normal property selectors and array iterators. More information can be found on:
					<a href="http://php.net/simplexml">http://php.net/simplexml</a>.
				</p>

				<h4><span class="{$extensionSPL}">{$extensionSPL}</span> SPL</h4>
				<p>
					SPL is a collection of interfaces and classes that are meant to solve standard problems. More information can be found
					on: <a href="http://php.net/SPL">http://php.net/SPL</a>.
				</p>

				<h4><span class="{$extensionPDO}">{$extensionPDO}</span> PDO</h4>
				<p>
					PDO provides a data-access abstraction layer, which means that, regardless of which database you're using, you use the
					same functions to issue queries and fetch data. More information can be found on: <a href="http://php.net/pdo">http://php.net/pdo</a>.
				</p>

				<h4><span class="{$extensionPDOMySQL}">{$extensionPDOMySQL}</span> PDO MySQL driver</h4>
				<p>
					PDO_MYSQL is a driver that implements the PHP Data Objects (PDO) interface to enable access from PHP to MySQL databases.
					More information can be found on: <a href="http://www.php.net/manual/en/ref.pdo-mysql.php">http://www.php.net/manual/en/ref.pdo-mysql.php</a>.
				</p>

				<h4><span class="{$extensionMBString}">{$extensionMBString}</span> mb_string</h4>
				<p>
					mbstring provides multibyte specific string functions that help you deal with multibyte encodings in PHP. In addition to
					that, mbstring handles character encoding conversion between the possible encoding pairs. mbstring is designed to handle Unicode-based
					encodings. More information can be found on: <a href="http://php.net/mb_string">http://php.net/mb_string</a>.
				</p>

				<h4><span class="{$extensionIconv}">{$extensionIconv}</span> iconv</h4>
				<p>
					This module contains an interface to iconv character set conversion facility. With this module, you can turn a string
					represented by a local character set into the one represented by another character set, which may be the Unicode
					character set. More information can be found on: <a href="http://php.net/iconv">http://php.net/iconv</a>.
				</p>

				<h4><span class="{$extensionGD2}">{$extensionGD2}</span> GD2</h4>
				<p>
					PHP is not limited to creating just HTML output. It can also be used to create and manipulate image files in a variety of
					different image formats. More information can be found on: <a href="http://php.net/gd">http://php.net/gd</a>.
				</p>

				<h3>PHP ini-settings</h3>
				<h4><span class="{$settingsSafeMode}">{$settingsSafeMode}</span> Safe Mode</h4>
				<p><strong>As of PHP 5.3.0 Safe Mode is deprecated.</strong> For forward compability we highly recommend you to disable Safe Mode.</p>

				<h4><span class="{$settingsOpenBasedir}">{$settingsOpenBasedir}</span> Open Basedir</h4>
				<p>For forward compability we highly recommend you not to use open_basedir.</p>

				<h3>Webserver</h3>
				<h4><span class="{$modRewrite}">{$modRewrite}</span> mod_rewrite</h4>
				<p>
					Fork CMS will not be able to run if mod_rewrite can not be applied. Please make sure that the .htaccess file is present (the file
					starts with a dot, so it may be hidden on your filesystem), being read (AllowOverride directive) and the mod_rewrite module is
					enabled in Apache. If you are installing Fork CMS on another webserver than Apache, make sure you have manually configured your
					webserver to properly rewrite urls. More information can be found in our
					<a href="http://www.fork-cms.com/knowledge-base/detail/fork-cms-and-webservers" title="Fork CMS and webservers">knowledge base</a>.
					If you are certain that your server is well configured, you may proceed the installation despite this warning.
				</p>

				<h3>Required permissions and/or files</h3>
				<h4><span class="{$fileSystemBackendCache}">{$fileSystemBackendCache}</span> {$PATH_WWW}/backend/cache/*</h4>
				<p>In this location all files created by the backend will be stored. This location and all subdirectories must be writable.</p>

				<h4><span class="{$fileSystemBackendModules}">{$fileSystemBackendModules}</span> {$PATH_WWW}/backend/modules/</h4>
				<p>In this location modules will be installed. You can continue the installation, but installing a module will then require a manual upload.</p>

				<h4><span class="{$fileSystemFrontendCache}">{$fileSystemFrontendCache}</span> {$PATH_WWW}/frontend/cache/*</h4>
				<p>In this location all files created by the frontend will be stored. This location and all subdirectories must be writable.</p>

				<h4><span class="{$fileSystemFrontendFiles}">{$fileSystemFrontendFiles}</span> {$PATH_WWW}/frontend/files/*</h4>
				<p>In this location all files uploaded by the user/modules will be stored. This location and all subdirectories must be writable.</p>

				<h4><span class="{$fileSystemFrontendModules}">{$fileSystemFrontendModules}</span> {$PATH_WWW}/frontend/modules/</h4>
				<p>In this location modules will be installed. You can continue the installation, but installing a module will then require a manual upload.</p>

				<h4><span class="{$fileSystemFrontendThemes}">{$fileSystemFrontendThemes}</span> {$PATH_WWW}/frontend/themes/</h4>
				<p>In this location themes will be installed. You can continue the installation, but installing a theme will then require a manual upload.</p>

				<h4><span class="{$fileSystemLibrary}">{$fileSystemLibrary}</span> {$PATH_LIBRARY}</h4>
				<p>This location must be writable for the installer, afterwards this folder only needs to be readable.</p>

				<h4><span class="{$fileSystemLibraryExternal}">{$fileSystemLibraryExternal}</span> {$PATH_LIBRARY}/external</h4>
				<p>This location must be writable for the installer, afterwards this folder only needs to be readable.</p>

				<h4><span class="{$fileSystemInstaller}">{$fileSystemInstaller}</span> {$PATH_WWW}/install</h4>
				<p>This location must be writable for the installer.</p>

				<h4><span class="{$fileSystemConfig}">{$fileSystemConfig}</span> {$PATH_LIBRARY}/config.base.php</h4>
				<p>This file is used to create the application config file.</p>

				<h4><span class="{$fileSystemGlobals}">{$fileSystemGlobals}</span> {$PATH_LIBRARY}/globals.base.php</h4>
				<p>This file is used to create the global configuration file.</p>

				<h4><span class="{$fileSystemGlobalsBackend}">{$fileSystemGlobalsBackend}</span> {$PATH_LIBRARY}/globals_backend.base.php</h4>
				<p>This file is used to create the global backend configuration file.</p>

				<h4><span class="{$fileSystemGlobalsFrontend}">{$fileSystemGlobalsFrontend}</span> {$PATH_LIBRARY}/globals_frontend.base.php</h4>
				<p>This file is used to create the global frontend configuration file.</p>

				<h4><span class="{$fileSystemPathLibrary}">{$fileSystemPathLibrary}</span> {$PATH_LIBRARY}</h4>
				<p>This directory is used to store your configuration files. The installer tries to find this directory automatically.</p>
			</div>
		</div>

		{option:!error}
		{option:warning}
		<div class="fullwidthOptions">
			<div class="buttonHolder">
				<a href="{$step3}" id="installerButton" class="inputButton button mainButton" name="installer">Install anyway</a>
			</div>
		</div>
		{/option:warning}
		{/option:!error}
	</div>
</form>

{$foot}