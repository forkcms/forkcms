<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="themes">Themes</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<ol>
					<li><a href="themes.php#howitworks">How themes work</a></li>
					<li><a href="themes.php#structure">Theme directory structure</a></li>
					<li><a href="themes.php#themeBlocks">Blocks</a></li>
					<li><a href="themes.php#footNotes">Footnotes</a></li>
				</ol>

				<h3 id="howitworks">How themes work</h3>

				<p>In order to get the most out of Fork CMS, it's very important to understand theming.</p>

				<p>For every new project, you should create a new theme. It's always easier to modify an existing theme to your needs than to build a new theme. Fork CMS provides a default theme to start from called <em>Scratch</em>. Follow the <a href="themes_tutorial.php">theme tutorial</a> to get started. This section contains general information on how themes work.</p>

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