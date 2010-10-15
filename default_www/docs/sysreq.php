<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="sysReqs">Detailed system requirements</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<p>Fork CMS is a web application, so it needs a we bserver. Below you will find the minimal requirements. Consult your system adminstrator for specifics: if you want to know if you can run Fork CMS on your webserver, refer him or her to this page.</p>

				<h3>Short list</h3>

				<ul>
					<li>Apache 2.0 with mod_rewrite enabled</li>
					<li>PHP 5.2 or greater
					<ul>
						<li>cURL</li>
						<li>SimpleXML</li>
						<li><span class="caps">SPL</span></li>
						<li><span class="caps">PDO</span></li>
						<li>mb_string</li>
						<li>iconv</li>
						<li>gd2</li>
					</ul></li>
					<li>MySQL 5.0</li>
				</ul>

				<h3>Resources</h3>

				<p>A standard installation will use around 10MB of disk space. But as soon as you start to publish content this can increase. We advice you to have 50MB of diskspace available.</p>

				<h3>Web server</h3>

				<p>Fork CMS is a web-application, so it needs a web-server. The platform was developed and fully tested on <a href="http://apache.org">Apache 2.0</a>, so this webserver is our recommended one. Because Fork CMS really focusses on SEO we need <code>Rewrite Engine</code> enabled for nice-looking URLs. Under Apache <code>Rewrite Engine</code> is available as a module: <code>mod_rewrite</code>.</p>
				<p>We know our application is running on different webservers (lighttp<sup class="footnote"><a href="#fn1">1</a></sup>, nginx, …), but won’t support them.</p>
				<p class="footnote"><sup id="fn1">1</sup> Use the error-handler to reroute all incoming requests to index.php</p>

				<h3>PHP</h3>

				<p>The code in our application is fully written in <a href="http://php.net">PHP</a>. We tried to use all capabilities of PHP 5.2, so this is the minimum required version.</p>
				<p>Some of the code tries to connect to external services (Akismet for spam-checking), this operates to cURL. In PHP there is an extension called <a href="http://php.net/curl">cURL</a> that makes it possible to connect and communicate with these external services. Most of these services are API’s that uses <span class="caps">XML</span> to communicate. In order to process the <span class="caps">XML</span> we use the <a href="http://php.net/simplexml">SimpleXML-extension</a>.</p>
				<p>Since PHP 5.3 the <a href="http://php.net/spl"><span class="caps">SPL</span>-extension</a> is loaded by default, so this is only important if you use PHP 5.2. <span class="caps">SPL</span> stands for Standard PHP Library and is a collection of interfaces and classes that can be use to solve standard problems.</p>
				<p>Fork CMS uses a database to store the data, for communicating with the database-server we make use of <a href="http://php.net"><span class="caps">PDO</span></a>. Which is a data-access abstraction layer.</p>
				<p>Our application is ready for <span class="caps">UTF</span>-8, but since this isn’t a native feature of PHP (will be in PHP 6), we make use of the <a href="http://php.net/mb_string">mb_string-extension</a>. mb_string is a collection of functions that can handle multibyte strings. Also we use <a href="http://php.net/iconv">iconv</a> to convert between different character-sets.</p>
				<p>Some of the modules in Fork CMS create images, in order to do so we use the <a href="http://php.net/gd">GD-library</a>. Make sure you install version 2.0.</p>

				<h3>Database</h3>
				<p>All the data of Fork CMS is stored in a <a href="http://mysql.com">MySQL</a> database. The minimum required version is MySQL 5.0.</p>
				<p>Make sure the user is granted privileges for: <span class="caps">SELECT</span>, <span class="caps">INSERT</span>, <span class="caps">UPDATE</span>, <span class="caps">DELETE</span>, <span class="caps">DROP</span>, <span class="caps">ALTER</span> and <span class="caps">CREATE</span>.</p>

			</div>
		</div>

		<div class="hr"><hr /></div>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>

</body>
</html>

