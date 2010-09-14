<?php require_once '_head.php'; ?>
<body>
	<div id="container">

		<?php require_once '_header.php'; ?>
		<?php require_once '_toc.php'; ?>

		<div class="id1">
			<div class="content">

				<p><span class="markedTodo">@note This documentation is unfinished. Please post in the <a href="http://groups.google.com/group/fork-cms/?pli=1">Google Group</a> if you get stuck.</span></p>

				<p class="intro">Welcome to the documentation of Fork CMS. You'll find everything you need here to get your Fork CMS powered website running up and quickly. Have fun!</p>

				<hr />

				<ol>
					<li><a href="index.php#whatis">What is Fork CMS?</a></li>
					<li><a href="index.php#download">Download</a></li>
					<li><a href="index.php#why">Why?</a></li>
					<li><a href="index.php#sysreq">System requirements</a></li>
					<li><a href="index.php#installation">Installation</a></li>
					<li><a href="index.php#bugReports">Bug reports</a></li>
					<li><a href="index.php#discussion">Discussion</a></li>
					<li><a href="index.php#thanks">Thanks!</a></li>
				</ol>

				<h3 id="whatis">What is Fork CMS?</h3>

 				<p>Fork CMS in a nutshell: Fork enables developers to deliver kick-ass websites. Marketing and communication managers to edit every part of their website using an easy and usable interface. It comes with good defaults and modules so you can get a solid website up and running quickly</p>

				<h3 id="download">Download</h3>

				<p>Download Fork CMS from Github: <span class="markedTodo">Note: this repository is private until we release our first open source version</span></p>

				<ul>
					<li>Use git to clone the latest release: <code class="marked">git clone git@github.com:forkcms/forkcms.git</code></li>
					<li>Alternatively, go to <a href="https://github.com/forkcms/forkcms/tree/">Fork CMS's Github page</a> and click the download button. You can choose to download a .zip or .tar file there.</li>
				</ul>

				<h3 id="why">Why should I use Fork CMS?</h3>

				<ul>
					<li><strong>Killer feature</strong>: finally a CMS that's easy to understand for your clients: they'll get the hang of using Fork quickly, since the interface is so easy and intuitive. You won't have to spend your days providing support.</li>
					<li>Fork is built using the <a href="http://www.spoon-library.com/">Spoon</a> PHP5 library. Using Spoon is easy: it's like taking candy from a baby. Spoon stands for speed, both in page execution and coding agility.</li>
					<li>All javascript has been written using jQuery, the best Javascript library in town</li>
					<li>Fork CMS has been built up from the ground to deliver speedy, performant websites. Who doesn't like speed?</li>
				</ul>

				<h3 id="sysreq">System requirements</h3>

				<p>After downloading Fork CMS, you'll have to install it. Typically you'll develop on a local copy before putting the website online. First, make sure your server matches the requirements:</p>

				<ul>
					<li>PHP 5.2 or higher</li>
					<li>The following PHP extensions should be installed and enabled: cURL, SimpleXML, SPL, PDO, PDO MySQL driver, mb_string, iconv, GD2</li>
					<li>MySQL 5.0</li>
					<li>Apache 2.0 with mod_rewrite enabled</li>
				</ul>
				
				<p>Please consult the <a href="sysreq.php">detailed system requirements</a> for more information.</p>

				<h3 id="installation">Installation</h3>

				<p>Point your localhost (e.g. <code>myforksite.local</code>) to the default_www path e.g. if your website lives in <code>/Users/accountname/Sites/mywebsite</code>, point your server to <code>/Users/accountname/Sites/mywebsite/default_www</code>.</p>

				<p>Visit to <code>&lt;your-domain&gt;/install</code> (e.g. http://myforksite.local/install) to start the installation.</p>

				<p>Have fun with your project!</p>

				<h3 id="bugReports">Bug reports</h3>

				<p>If you encounter any bugs, please create a new issue at <a href="https://github.com/forkcms/forkcms/issues">https://github.com/forkcms/forkcms/issues</a> <span class="markedTodo">Note: this repository is private until we release our first open source version</span></p>

				<h3 id="discussion">Discussion</h3>
				
				<p>If you encounter a problem, or want to discuss Fork CMS, visit the <a href="http://groups.google.com/group/fork-cms/">Fork CMS google group</a>. You have to be a member to post a new message.</p>

			</div>
		</div>

		<div class="hr"><hr /></div>

	<p class="secondaryContent">&copy;Fork CMS by <a href="http://www.netlash.com">Netlash</a> and contributors. See <a href="../LICENSE" rel="license">the Fork CMS license</a> for usage details.</p>

</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>

</body>
</html>