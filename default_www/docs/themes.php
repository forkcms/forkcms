<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="themes">Themes</h2>

		<div class="cols id1">
			<div class="col col-8 content">
				<div class="col-6">
					<h3 id="howitworks">How themes work</h3>

					<p>In order to get the most out of Fork CMS, it's very important to understand theming.</p>

					<p>For every new project, you should create a new theme. It's (pretty much) always easier to modify an existing theme to your needs than to build a new theme from scratch.</p>

					<h3 id="structure">Theme directory structure</h3>

					<p>The themes folder is located in default_www/frontend/themes.</p>

					This is the directory structure of a theme:

					<pre class="brush: xml;">
					theme_name
					|-- core
					|   |-- css
					|   |   `-- screen.css
					|   `-- templates
					|       |-- home.tpl
					|       |-- contentpage.tpl
					`-- modules
					    `-- blog
					        `-- templates
					            |-- detail.tpl
					            `-- index.tpl</pre>

					<h3 id="overrides">Overrides</h3>

					<p>Consider the directory structure of Fork CMS.</p>

					<p>When a page is requested, the template engine kicks in. </p>

					<p><span class="markedTodo">@todo continue explanation</span></p>

					<h3 id="templateModifiers">Modifiers</h3>

					<ul>
						<li>createhtmllinks</li>
						<li>date</li>
						<li>htmlentities</li>
						<li>lowercase</li>
						<li>ltrim</li>
						<li>nl2br</li>
						<li>repeat</li>
						<li>rtrim</li>
						<li>shuffle</li>
						<li>sprintf</li>
						<li>stripslashes</li>
						<li>substring</li>
						<li>trim</li>
						<li>ucfirst</li>
						<li>ucwords</li>
						<li>uppercase</li>
					</ul>

					<p>Documentation: search for &#8220;List of default modifiers&#8221; <a href="http://tutorials.spoon-library.be/details/templates-part-3">here</a></p>
				</div>

				<table class="datagrid" cellspacing="0">
					<tbody>
						<tr>
							<th>Modifier</th>
							<th>Description</th>
							<th>Syntax</th>
							<th>&nbsp;</th>
						</tr>
						<tr>
								<td>cleanupplaintext</td>
								<td>Formats plain text as <span class="caps">HTML</span>, links will be detected, paragraphs will be inserted</td>
								<td><code>{$var|cleanupplainText}</code></td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td>getnavigation</td>
								<td>Get the navigation html</td>
						                <td><code>{$var|getnavigation[:'&lt;type&gt;'][:&lt;parentId&gt;][:&lt;depth&gt;][:'&lt;excludeIds-splitted-by-dash&gt;']}</code></td>
								<td>available types: page, meta (if the user setting is enabled), footer</td>
						</tr>
						<tr>
								<td>getsubnavigation</td>
								<td>Get the navigation html</td>
						                <td><code>{$var|getsubnavigation[:'&lt;type&gt;'][:&lt;pageId&gt;][:&lt;startdepth&gt;][:&lt;endDepth&gt;][:'&lt;excludeIds-splitted-by-dash&gt;']}</code></td>
								<td>available types: page, meta (if the user setting is enabled)</td>
						</tr>
						<tr>
								<td>timeago</td>
								<td>Formats a timestamp as a string that indicates the time ago</td>
								<td><code>{$var|timeago}</code></td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td>truncate</td>
								<td>Truncate a string</td>
								<td><code>{$var|truncate:&lt;length&gt;[:useHellip]}</code></td>
								<td>useHellip: possible values: true, false</td>

						</tr>
						<tr>
								<td>usersetting</td>
								<td>Get the value for a backend user-setting</td>
								<td><code>{$var|userSetting:'&lt;setting&gt;'[:&lt;userId&gt;]}</code></td>
								<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>

				<div class="col-6">
					<h3 id="constants">Available constants</h3>

					<ul>
						<li>
							<strong>FRONTEND_CACHE_PATH</strong>: The path to the frontend cache-folder, eg: /home/fork/default_www/frontend/cache
						</li>
						<li>
							<strong>FRONTEND_CACHE_URL</strong>: The url to the frontend cache-folder, eg: /frontend/cache
						</li>
						<li>
							<strong>FRONTEND_CORE_PATH</strong>: The path to the frontend core-folder, eg: /home/fork/default_www/frontend/core
						</li>
						<li>
							<strong>FRONTEND_CORE_URL</strong>: The url to the frontend core-folder, eg: /frontend/core
						</li>
						<li>
							<strong>FRONTEND_FILES_PATH</strong>: The path to the frontend files-folder, in this folder you can store files that are uploaded by a user, eg: /home/fork/default_www/frontend/files
						</li>
						<li>
							<strong>FRONTEND_FILES_URL</strong>: Absolute url to the frontend files-folder, eg: /frontend/files
						</li>
						<li>
							<strong>FRONTEND_MODULES_PATH</strong>: Path to the frontend modules folder, eg: /home/fork/default_www/frontend/modules
						</li>
						<li>
							<strong>FRONTEND_PATH</strong>: Path to the frontend, eg: /home/fork/default_www/frontend
						</li>
						<li>
							<strong>LANGUAGE</strong>: The current language the user is working on, eg: nl
						</li>
						<li>
							<strong>PATH_LIBRARY</strong>: Path to the library, eg: /home/fork/library
						</li>
						<li>
							<strong>PATH_WWW</strong>: Path to the folder that has to be used as document-root, eg: /home/fork/default_www
						</li>
						<li>
							<strong>SITE_DOMAIN</strong>: The primary domain for the site, eg: forkng.local
						</li>
						<li>
							<strong>SITE_DEFAULT_LANGUAGE</strong>: The default language for the site, eg: nl
						</li>
						<li>
							<strong>SITE_MULTILANGUAGE</strong>: Is the site available in multiple languages?, eg: true
						</li>
						<li>
							<strong>SITE_DEFAULT_TITLE</strong>: The default title for the site, can be used as fallback. eg: Fork NG
						</li>
						<li>
							<strong>SITE_TITLE</strong> The title for the site, configured by the user, eg: Fork NG
							<ul>
								<li>Usage in templates: {$SITE_TITLE}
								</li>
								<li>Usage in frontend PHP: FrontendModel::getModuleSetting(‘core’, ‘site_title_’. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
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
								<li>Usage in frontend PHP: FrontendModel::getModuleSetting(‘core’, ‘theme’, ‘default’);
								</li>
							</ul>
						</li>
						<li>
							<strong>THEME_PATH</strong>: The path to the theme that is currently in use, eg: /home/fork/default_www/frontend/themes/default
							<ul>
								<li>Usage in templates: {$THEME_PATH}
								</li>
								<li>Usage in frontend PHP: FRONTEND_PATH . ‘/themes/’. FrontendModel::getModuleSetting(‘core’, ‘theme’, ‘default’);
								</li>
							</ul>
						</li>
						<li>
							<strong>THEME_URL</strong>: The url to the theme that is currently in use, eg: /frontend/themes/default
							<ul>
								<li>Usage in templates: {$THEME_URL}
								</li>
								<li>Usage in frontend PHP: ‘/frontend/themes/’. FrontendModel::getModuleSetting(‘core’, ‘theme’, ‘default’);
								</li>
							</ul>
						</li>
					</ul>
				</div>

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