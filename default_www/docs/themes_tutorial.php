<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2>Themes tutorial</h2>

		<div class="cols" id="themeTutorial">
			<div class="col col-6">

				<ul class="themeTutorial">
					<li class="content">
						<img src="images/theme_tutorial/1.png" width="1024" height="617" alt="1" />
						<p>We're going to explain the themes with a basic website using the scratch theme. The scratch theme contains few functionalities and is specifically built for this tutorial.</p>
						<p>There's a few steps you're going to have to do first to make sure you're working with the correct data.</p>
						<ul>
							<li>Download the <a href="downloads/fork_scratch.sql">tutorial database</a></li>
							<li>Import this database in a new database your local PHPMyAdmin</li>
							<li>Change the database to connect to in library/globals.php</li>
							<li>Remove all cache files from the default install, see "<a href="extra.php#reinstalling">Reinstalling Fork CMS</a>"</li>
						</ul>
						<p>If you're done, point your browse to your Fork install and you should see a website that looks like the above screenshot. Time to get started!</p>
					</li>
					<li>
						<p>This website has three pages: Home, About us and Contact. The website also contains a sitemap, a disclaimer (links in the footer). Kind of boring, isn't it? Let’s spice things up by adding a new template with two columns instead of the current 1 column.</p>
					</li>
					<li>
						<img src="images/theme_tutorial/9.png" width="1024" height="617" alt="9" />
						<p>Point your browser to http://&lt;your-domain&gt;/private to log in to the backend.</p>
					</li>
					<li>
						<img src="images/theme_tutorial/10.png" width="1024" height="617" alt="10" />
						<p>Enter your details and click the log in button.</p>
					</li>
					<li>
						<img src="images/theme_tutorial/11.png" width="1024" height="617" alt="11" />
						<p>You'll arrive at the dashboard (which is kind of empty here, since our site currently doesn't have too much functionalities).</p>
						<p>Click "Settings".</p>
					</li>
					<li>
						<img src="images/theme_tutorial/12.png" width="1017" height="1466" alt="12" />
						<p>On the "Settings" page click "Advanced".</p>
					</li>
					<li>
						<img src="images/theme_tutorial/13.png" width="1032" height="617" alt="13" />
						<p>If you're following along, make sure your theme is set to "Scratch" (and make sure you're using the correct database, see step 1).</p>
					</li>
					<li>
						<img src="images/theme_tutorial/14.png" width="1032" height="617" alt="14" />
						<p>Click Templates and you'll see a few default templates (the "core" and "default" templates are used when you don't have any theme set. We don't recommend using them. Don't hack the core!).</p>
					</li>
					<li>
						<img src="images/theme_tutorial/15.png" width="1017" height="718" alt="15" />
						<p>If you edit the Scratch - Default template you should see this. Note that this theme is set to "Default" meaning any new page will automatically have this template. The page contains 1 block, which is called <em>Main Content</em> and it's default type is set to Editor.</p>
					</li>
					<li>
						<img src="images/theme_tutorial/16.png" width="1063" height="593" alt="16" />
						<p>Create a new template under frontend/themes/scratch/core/templates called twocolumns.tpl</p>
					</li>
					<li>
						<img src="images/theme_tutorial/17.png" width="1017" height="753" alt="17" />
						<p>Enter 'twocolumns.tpl' in the path to the template field. We like to prefix our templates with the theme name so it's clear in the overview which theme they belong to: so enter 'Scratch - Two columns' as the template label.</p>
						<p>Since we're going to make a two column we're going to need 2 editable fields. Set the number of blocks to 2, add labels to both, and leave them as 'Editor'.</p>
						<p>The field labelled <strong>Layout</strong> is a special one. This field uses a mini-language to create an approximation of your layout, so that when you edit the template contents in the Pages module, you can see which content goes where. This will all become clear in a minute. Enter '[1, 2]' for now and hold on!</p>
					</li>
					<li>
						<img src="images/theme_tutorial/18.png" width="1032" height="617" alt="18" />
						<p>Click the save button  and the template will be added.</p>
					</li>
					<li>
						<img src="images/theme_tutorial/19.png" width="1017" height="684" alt="19" />
						<p>To see our new template, we're going to need a page that has the template. Go to Pages and click "Add new page". Give the page a name: I used "Two columns test".</p>
					</li>
					<li>
						<img src="images/theme_tutorial/20.png" width="1032" height="617" alt="20" />
						<p>You’re going to want to change the template by going to the template Tab. This template contains 1 block, which is an Editor (hence the one editor labelled Main Content in the first tab). Click "Edit template"</p>

					</li>
					<li>
						<img src="images/theme_tutorial/21.png" width="1033" height="618" alt="21" />
						<p>Now select the template you just created "Scratch - Two columns".</p>
					</li>
					<li>
						<img src="images/theme_tutorial/22.png" width="1032" height="617" alt="22" />
						<p>You'll see that the graphical representation of the template looks different now. This because of the code [1,2] that was entered in the layout field. Notice the two boxes are labelled "Column left" and "Column right", the labels I gave the different blocks when creating the template.</p>
						<p>Now, how does the layout code work?</p>

						<ul>
							<li>If you want to add a row, use []</li>
							<li>If you want to display a block, use the block number</li>
							<li>If you want to add an empty cell (e.g. a non-editable area) use /</li>
							<li>If you want a block to be wider or bigger in it's graphical representation, repeat the number multiple times (can be done on multiple rows too: but the shape should form a rectangle)</li>
						</ul>
						<p>A more complex template would look like this:</p>
						<pre class="brush: plain;">
						[/,/,/,/,1],
						[/,2,2,2,2],
						[/,2,2,2,2],
						[/,3,4,4,4],
						[/,5,5,5,6],
						[/,5,5,5,7],
						[/,5,5,5,/],
						[/,8,8,8,9],
						</pre>

						<p>This is the visual representation of this template:</p>
						<img src="images/theme_tutorial/ncube.png" width="1010" height="1140" alt="Ncube" />

					</li>
					<li>
						<img src="images/theme_tutorial/23.png" width="1017" height="718" alt="23" />
						<p>Now go to the content tab and add some content so we can see our template in action.</p>
					</li>
					<li>
						<p>This wraps up the first theme tutorial. To be continued!</p>
					</li>
				</ul>
				
			</div>
		</div>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.defaults['gutter'] = false;
		SyntaxHighlighter.all();
	</script>

</body>
</html>