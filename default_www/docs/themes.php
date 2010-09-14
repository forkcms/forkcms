<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="themes">Themes</h2>

		<div class="cols id1">
			<div class="col col-8 content">

				<ol>
					<li><a href="themes.php#howitworks">How themes work</a></li>
					<li><a href="themes.php#structure">Theme directory structure</a></li>
					<li><a href="themes.php#themeBlocks">Blocks</a></li>
					<li><a href="themes.php#footNotes">Footnotes</a></li>
				</ol>

				<h3 id="howitworks">How themes work</h3>

				<p>In order to get the most out of Fork CMS, it's very important to understand theming.</p>

				<p>For every new project, you should create a new theme. It's always easier to modify an existing theme to your needs than to build a new theme from scratch. Fork CMS provides a default theme to start from called <em>Scratch</em>. We'll take this theme as our example from now on.</p>

				<h3 id="structure">Theme directory structure</h3>

				<p>The themes folder is located in <code>default_www/frontend/themes</code>. After a fresh install, this folder will contain just one theme: <em>scratch</em>.</p>

				<pre class="brush: xml;">
				scratch
				`-- core
				    |-- css
				    |   |-- ie6.css
				    |   |-- ie7.css
				    |   |-- print.css
				    |   `-- screen.css
				    |-- images
				    `-- templates
				        |-- _footer.tpl
				        |-- _head.tpl
				        `-- default.tpl
				</pre>

				<p>This theme contains a folder for your CSS and a folder for images. It also contains 1 templat: <code>default.tpl</code>. The other template files <code>_head.tpl</code> and <code>_footer.tpl</code> are partial templates<sup><a href="#footnote1">1</a></sup>.</p>

				<h3 id="themeBlocks">Blocks</h3>

				<p>Before you go on, make sure you have read <a href="pages.php#howTheTreeWorks">How the tree works</a>.</p>

				<p>This is what the homepage of the scratch template looks like:</p>

				<img src="images/scratch_home.png" width="218" height="282" alt="Scratch Home" />

			<p>Blocks are the primary way to build a template. A template will typically contain a few blocks, signified in the code like this:</p>

			<pre class="brush: xml;">
				{option:block1IsHTML}{$block1}{/option:block1IsHTML}
				{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
			</pre>

			<p>Each block will display a piece of content from the backend. There are <strong>3 types of blocks</strong>: editor, module and widget. Let's ignore the last two types and focus on the editor type first.</p>

			<h4>Two column template with 2 blocks</h4>

			<p>Let's consider the default template first.</p>

			<pre class="brush: xml;">
				{include:file=&#x27;{$THEME_PATH}/core/templates/_head.tpl&#x27;}
				&lt;body id=&quot;home&quot; class=&quot;{$LANGUAGE}&quot;&gt;
					&lt;div id=&quot;container&quot;&gt;

						&lt;div id=&quot;header&quot;&gt;
							&lt;h1&gt;&lt;a href=&quot;/&quot;&gt;{$siteTitle}&lt;/a&gt;&lt;/h1&gt;
							&lt;div id=&quot;navigation&quot;&gt;
								{$var|getnavigation:&#x27;page&#x27;:0:1}
							&lt;/div&gt;
						&lt;/div&gt;
						&lt;div id=&quot;main&quot;&gt;
							{option:block1IsHTML}{$block1}{/option:block1IsHTML}
							{option:!block1IsHTML}{include:file=&#x27;{$block1}&#x27;}{/option:!block1IsHTML}
						&lt;/div&gt;

						{include:file=&#x27;{$THEME_PATH}/core/templates/_footer.tpl&#x27;}

					&lt;/div&gt;
				&lt;/body&gt;
				&lt;/html&gt;
			</pre>

			<h4>Adding the template in the backend</h4>

			<p>Go to Settings > Templates and click the "Add template button".</p>

			<h4>Default blocks</h4>

			<p>(...)</p>

			<h4>Assigning the template to a page</h4>

			<p>(...)</p>

				<h3 id="footNotes">Footnotes</h3>

				<p><strong id="footnote1">1:</strong> The underscores in the filenames signify partial templates; this is not a requirement, but it's a helpful convention to separate real templates from partial ones.</p>

			</div>
		</div>
		
		<div class="hr"><hr /></div>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.defaults['gutter'] = false;
		SyntaxHighlighter.all();
	</script>

</body>
</html>