<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="installation">How to install</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<h3 id="sysreq">System requirements</h3>

				<p>After downloading Fork CMS, you'll have to install it. Typically you'll develop on a local copy before putting the website online. First, make sure your server matches the system requirements:</p>

				<ul>
					<li>PHP 5.2 or higher</li>
					<li>The following PHP extensions should be installed and enabled: cURL, SimpleXML, SPL, PDO, PDO MySQL driver, mb_string, iconv, GD2</li>
					<li>MySQL 5.0</li>
					<li>Apache 2.0 with mod_rewrite enabled</li>
				</ul>

				<p>Please consult the <a href="sysreq.php">detailed system requirements</a> for more information.</p>

				<h3 id="installation">Installation</h3>

				<p>Point your localhost (e.g. <code>myforksite.local</code>) to the default_www path e.g. if your website lives in <code>/Users/accountname/Sites/mywebsite</code>, point your server to <code>/Users/accountname/Sites/mywebsite/default_www</code>.</p>

				<p>Visit <code>&lt;your-domain&gt;/install</code> (e.g. http://myforksite.local/install) to start the installation.</p>

				<p>Have fun with your project!</p>

				<div class="hr"><hr /></div>

				<h4>I want to install Fork CMS but I have no access to the folder(s) above my document root/public folder.</h4>

				<p>Simply move the <code>library/</code> folder inside <code>default_www</code>. The installler will detect this.</p>

				<h4 id="reinstalling">Reinstalling Fork CMS</h4>
				<p>To reinstall Fork CMS — a reinstall as in, you previously installed and ran Fork CMS from the same disk location — do the following:</p>
				<ul>
					<li>Delete the file installed.txt in the default_www/install/cache folder.</li>
					<li>Delete all of cache files by running the remove_cache script in tools/remove_cache</li>
					<li>Install Fork again by visiting <code>&lt;your-domain&gt;/install</code>; follow the installer steps.</li>
					<li>Log in and go to Pages and resave a page to update the navigation cache. Done!</li>
				</ul>

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
