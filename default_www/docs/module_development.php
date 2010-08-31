<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="moduleDevelopment">Module development</h2>

		<div class="cols id1">
			<div class="col col-8 content">
				<div class="col-6">

					<h3 id="howitworks">Developing custom modules</h3>

					<p><span class="markedTodo">@todo write introduction</span></p>
					
					<p>The backend is the application that makes it possible for the user the manage the content (which is the mean task for a CMS). The backend is build as a modular system.</p>

					<h3 id="backendConstants">Constants</h3>

					<p>As the name suggests, the value of a constant cannot change during the execution of the script.</p>

					<p>In templates, use curly brackets ({}) and a dollar sign ($) to use a constant. E.g. let's say you want to link an image from the core/images folder of your theme.</p>

					<pre class="brush: xml;">
						&nbsp;
						&lt;img src=&quot;{$THEME_PATH}/core/images/logo.gif&quot; /&gt;
						&nbsp;
					</pre>

					<p>In PHP, use the constant itself. These constants are available in the backend.</p>

					<ul>
						<li>
							<strong>ACTION</strong>: In this constant the current action will be stored.
							<ul>
								<li>Usage in templates: {$ACTION}
								</li>
								<li>Usage in backend PHP: $this-&gt;URL-&gt;getAction();
								</li>
							</ul>
						</li>
						<li>
							<strong>BACKEND_CACHE_PATH</strong>: The path where the cache-folder is located, eg: /home/fork/default_www/backend/core
						</li>
						<li>
							<strong>BACKEND_CACHE_URL</strong>: The absolute URL to the cache folder, eg: /backend/cache
						</li>
						<li>
							<strong>BACKEND_CORE_PATH</strong>: The path to the core-folder, eg: /home/fork/default_www/backend/core
						</li>
						<li>
							<strong>BACKEND_CORE_URL</strong>: The absolute URL to the core-folder, eg: /backend/core
						</li>
						<li>
							<strong>BACKEND_MODULES_PATH</strong>: The path to the modules-folder, eg: /home/fork/default_www/backend/modules
						</li>
						<li>
							<strong>BACKEND_MODULE_PATH</strong>: The path to the current module-folder, eg: /home/fork/default_www/backend/modules/blog
						</li>
						<li>
							<strong>BACKEND_PATH</strong>: The path to the backend, eg: /home/fork/default_www/backend
						</li>
						<li>
							<strong>FORK_VERSION</strong>: The current version of Fork, eg: 2.0.0
						</li>
						<li>
							<strong>FRONTEND_CACHE_PATH</strong>: The path to the frontend cache-folder, eg: /home/fork/default_www/frontend/cache
						</li>
						<li>
							<strong>FRONTEND_CORE_PATH</strong>: The path to the frontend core-folder, eg: /home/fork/default_www/frontend/core
						</li>
						<li>
							<strong>FRONTEND_FILES_PATH</strong>: The path to the frontend files-folder, in this folder you can store files that are uploaded by a user, eg: /home/fork/default_www/frontend/files
						</li>
						<li>
							<strong>FRONTEND_FILES_URL</strong>: Absolute url to the fronten files-folder, eg: /frontend/files
						</li>
						<li>
							<strong>FRONTEND_MODULES_PATH</strong>: Path to the frontend modules folder, eg: /home/fork/default_www/frontend/modules
						</li>
						<li>
							<strong>FRONTEND_PATH</strong>: Path to the frontend, eg: /home/fork/default_www/frontend
						</li>
						<li>
							<strong>INTERFACE_LANGUAGE</strong>: The interface language for the current authenticated user, eg: nl
							<ul>
								<li>Usage in templates: {$INTERFACE_LANGUAGE}
								</li>
								<li>Usage in backend PHP: BackendAuthentication::getUser()-&gt;getSetting('interface_language');
								</li>
							</ul>
						</li>
						<li>
							<strong>LANGUAGE</strong>: The current language the user is working in, eg: nl
							<ul>
								<li>Usage in templates: {$LANGUAGE}
								</li>
								<li>Usage in backend PHP: BackendLanguage::getWorkingLanguage();
								</li>
							</ul>
						</li>
						<li>
							<strong>MODULE</strong>: The current module, eg: blog
							<ul>
								<li>Usage in templates: {$MODULE}
								</li>
								<li>Usage in backend PHP: $this-&gt;URL-&gt;getModule();
								</li>
							</ul>
						</li>
						<li>
							<strong>NAMED_APPLICATION</strong>: The name the user used for the application, eg: private
						</li>
						<li>
							<strong>PATH_LIBRARY</strong>: Path to the library, eg: /home/fork/library
						</li>
						<li>
							<strong>PATH_WWW</strong>: Path to the folder that has to be used as document-root, eg: /home/fork/default_www
						</li>
						<li>
							<strong>SITE_DEFAULT_LANGUAGE</strong>: The default language for the site, eg: nl
						</li>
						<li>
							<strong>SITE_DEFAULT_TITLE</strong>: The default title for the site, can be used as fallback. eg: Fork NG
						</li>
						<li>
							<strong>SITE_DOMAIN</strong>: The primary domain for the site, eg: forkng.local
						</li>
						<li>
							<strong>SITE_MULTILANGUAGE</strong>: Is the site available in multiple languages?, eg: true
						</li>
						<li>
							<strong>SITE_TITLE</strong>: The title for the site, configured by the user, eg: Fork NG
							<ul>
								<li>Usage in templates: {$SITE_TITLE}
								</li>
								<li>Usage in backend PHP: BackendModel::getSetting('core', 'site_title_'. BackendLanguage::getWorkingLanguage(), SITE_DEFAULT_TITLE);
								</li>
							</ul>
						</li>
						<li>
							<strong>SITE_URL</strong>: The full URL for the site, eg: http://fork-cms.be
						</li>
						<li>
							<strong>THEME</strong>: The theme that is currently in use, eg: default
							<ul>
								<li>Usage in templates: {$THEME}
								</li>
								<li>Usage in backend PHP: BackendModel::getSetting('core', 'theme', 'default'));
								</li>
							</ul>
						</li>
						<li>
							<strong>THEME_PATH</strong>: The path to the theme that is currently in use, eg: /home/fork/default_www/frontend/themes/default
							<ul>
								<li>Usage in templates: {$THEME_PATH}
								</li>
								<li>Usage in backend PHP: FRONTEND_PATH . '/themes/'. BackendModel::getSetting('core', 'theme', 'default');
								</li>
							</ul>
						</li>
					</ul>

				</div>

				<h3 id="backendModifiers">Template modifiers</h3>

				<table class="datagrid">
					<tbody>
						<tr>
							<th>Modifier</th>
							<th>Description</th>
							<th>Syntax</th>
							<th>&nbsp;</th>
						</tr>
						<tr>
							<td>geturl</td>
							<td>Convert a var into a <span class="caps">URL</span></td>
							<td><code>{$var|geturl:'&lt;action&gt;'[:'&lt;module&gt;']}</code></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>getmainnavigation:</td>
							<td>Convert a var into main-navigation-html</td>
							<td><code>{$var|getmainnavigation}</code></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>getnavigation</td>
							<td>Convert a var into navigation-html</td>
							<td><code>{$var|getnavigation:'startdepth'[:'maximumdepth']}</code></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>rand</td>
							<td>Random number between two values</td>
							<td><code>{$var|rand:1:3}</code></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>dump</td>
							<td>dumps the variable</td>
							<td><code>{$var|dump}</code></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>truncate</td>
							<td>Truncate a string</td>
							<td><code>{$var|truncate:&lt;length&gt;[:usehellip]}</code></td>
							<td>useHellip: possible values: true, false</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
		
		<div class="hr"><hr /></div>

</div>
	
	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>

	</div>
</body>
</html>