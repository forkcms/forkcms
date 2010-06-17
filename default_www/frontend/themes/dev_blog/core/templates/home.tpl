{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE}">
	<div id="container">

		<!-- {$var|getnavigation} -->

		<div id="sidebar">
			<h1><a href="/">{$siteTitle}</a></h1>

			<div class="contentBlocks">
				<div class="contentBlock">
					<h3>What’s this?</h3>
					<p>This blog is about Fork CMS, a kickass open source Content Management System.</p>
				</div>
				<div id="newsletterSubscribe" class="contentBlock">
					<h3>Don’t miss out!</h3>
					<p>Want to stay up to date? Subscribe to our e-mail newsletter and make sure you don’t miss a thing.</p>
					<form action="home_submit" method="post" class="forkForms">
						<p><input type="text" class="inputText" /></p>
						<p><a class="button" href="#">Inschrijven</a></p>
					</form>
					<p>Alternatively, subscribe to the <a href="#">RSS feed</a>.</p>
					
				</div>
			</div>

		</div>

		<div id="content">
			
			<div id="blog" class="home">
				<div class="article">
					<div class="heading">
						<h2><a href="#">Screenshot friday</a></h2>
						<ul>
							<li>Published by Wolf, friday june 18th, 12:29</li>
							<li class="lastChild"><a href="#'">21 comments</a></li>
						</ul>
					</div>
					<div class="content">
						<p>Here’s a few things we’ve been working on:</p>
						
						<div class="figure">
							<img src="/frontend/themes/dev_blog/core/images/fork_screenshot_1.jpg" alt="" />
							<p class="caption">We’ve been working on localization.</p>
						</div>

						<div class="figure">
							<img src="/frontend/themes/dev_blog/core/images/fork_screenshot_2.jpg" alt="" />
							<p class="caption">We’ve been working on localization.</p>
						</div>

					</div>
				</div>
				<div class="article">
					<div class="heading">
						<h2><a href="#">Introducing</a></h2>
						<ul>
							<li>Published by Wolf, thursday june 17th, 14:29</li>
							<li class="lastChild"><a href="#'">26 comments</a></li>
						</ul>
					</div>
					<div class="content">
						<p>Welcome to the new Fork CMS blog.</p>
						<h4>What?</h4>
						<p>We are open sourcing our content management system for the world to use. Fork CMS has been in use internally at our company for over three years. We’re building a new major version and you’re invited to the ride. We’re committed to building the best CMS out there.</p>
						<h4>Why?</h4>
						<p>Our main goal is to help anyone responsible for a website kick ass at their job. To enable them to communicate their message they way they want to. To start a conversation.</p>
						<p>Too many websites don’t change for over a year because they require a call to a web developer to update. That guy then sends over his bill and is frustrated that all of his clients can’t figure out his CMS. Those CMSes are either super-hacked Wordpress installs that nobody “gets” anymore, or overly complicated Drupal/Joomla installs that no non-developer dares to update.</p>
						<p>We believe that the launch of a website is just the start. We believe anyone should be able to update any part of a website. We believe in a user interface without developer-speak. We believe putting something into a setting is a sign of weakness. We think modes make confusing software, and that a UI should be as simple as possible.</p>
						<p>We are web developers. We want to code in a friendly and clean environment. We want structure and cleanliness. We hate trailing whitespace. We built a tool to help us create the best websites possible. Joel Spolsky says you have to define what your company is about.</p>
						<blockquote><p>(…) if you can’t explain your mission in the form, “We help $TYPEOFPERSON be awesome at $THING,” you are not going to have passionate users. What’s your tagline? Can you fit it into that template?</p></blockquote>
						<p>We help website owners to be awesome at communication.</p>
						<h4>Who?</h4>
						<p>We are Netlash, a web agency based in Ghent, Belgium. We’re eighteen people who build websites day in, day out. We build small business websites like Equazion. We build e-commerce shops like Cookstore and community websites like AB Concerts. In order to make these websites awesome, we built Fork CMS.</p>
						<h4>Stay up to date</h4>
						<p>Subscribe to the <a href="#">RSS feed</a>.</p>
					</div>

				</div>
			</div>
			
			
			{* Block 0 *}
			<!-- {option:block0IsHTML}{$block0}{/option:block0IsHTML}
			{option:!block0IsHTML}{include:file='{$block0}'}{/option:!block0IsHTML} -->


			{* Block 1 *}
			<!-- {option:block1IsHTML}{$block1}{/option:block1IsHTML}
			{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML} -->

			{* Block 2 *}
			<!-- {option:block2IsHTML}{$block2}{/option:block2IsHTML}
			{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML} -->
			
		</div>

	</div>
</body>
</html>