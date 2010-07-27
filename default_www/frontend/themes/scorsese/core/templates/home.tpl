{include:file='{$THEME_URL}/core/templates/head.tpl'}

<body id="home" class="{$LANGUAGE}">

	<div id="container">
		<div id="header">
			<div id="logo">
				<h2><a href="/">{$siteTitle}</a></h2>
			</div>

			<div id="top">

				<div id="metaNavigation">
					{$var|getnavigation:'meta':0:1}
				</div>

				{include:file='{$THEME_URL}/layout/templates/languages.tpl'}

				<form action="#" method="get">
					<fieldset>
						<input type="text" name="q" id="q" class="inputText" value="" />
						<input type="submit" name="search" value="Search" class="inputSubmit" />
					</fieldset>
				</form>
			</div>

			<div id="navigation">
				<ul>
					<li><a href="#" title="Home">Home</a></li>
					<li><a href="#" title="Filmography">Filmography</a></li>
					<li><a href="#" title="Blog">Blog</a></li>
					<li><a href="#" title="Extra item">Extra item</a></li>
					<li><a href="#" title="Extra item 2">Extra item 2</a></li>
				</ul>
			</div>
		</div>

		<div id="main">
			<div id="intro">
				<div class="imageWrapper floatLeft">
					<img src="{$THEME_URL}/core/images/img-intro.jpg" alt="On Shutter Island set" title="On Shutter Island set" />
					<span>Taken at the set of Shutter Island. Martin directing</span>
				</div>
				<p class="content highLight">Martin C. Scorsese is an American film director, screenwriter, producer, actor, and film historian.</p>
				<p class="content">Welcome to <strong>scorcese</strong>. Consult <a href="#" title="about">about</a> to learn about Martin Scorsese: his biography, his life, what inspired him to be a filmmaker. See the complete <a href="#" title="filmography">filmography</a> or stay up to date by following the <a href="#" title="blog">blog</a> (there&#180;s an <a href="#" title="RSS feed">RSS feed</a> too!). Lastly if you have any questions, look at our contact form. Have fun.</p>
			</div>

			<div id="content">
				<div id="contentWrapper">
					<div id="mainContent">
						<div class="content article">
							<h3><a href="#">This is the title of a blog post</a></h3>
							<p class="date">Semptember 19th, 2010 - <a href="#">3 comments</a></p>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero.</p>
							<p>Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>
							<p class="meta">Written by <a href="#">Wolf</a> in the category: <a href="#">general</a>. Tags: <a href="#">alpha</a>, <a href="#">beta</a>, <a href="#">gamma</a></p>
						</div>

						<p class="buttonHolder">
							<a class="button" href="#">Previous articles</a>
						</p>
					</div>

					<div id="sideContent">
						<div class="sideBlock firstChild">
							<h3>Latest articles</h3>
							<ul>
								<li class="firstChild"><a href="#">Title of a blog post</a></li>
								<li><a href="#">Martin the film historiam</a></li>
								<li><a href="#">New York Film school</a></li>
							</ul>
						</div>
						<div class="sideBlock">
							<h3>Latest comments</h3>
							<ul>
								<li class="firstChild"><a href="#">Steven F.</a> commented on <a href="#">Title of a blog post</a></li>
								<li><a href="#">Roger Eckbertc</a> commented on <a href="#">Martin the film historiam</a></li>
								<li><a href="#">Mememe</a> commented on <a href="#">New York Film school</a></li>
							</ul>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>


	{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}

	<!-- {option:!hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:!hideContentTitle} -->

	{* Block 1 *}
	<!-- {option:block1IsHTML}{$block1}{/option:block1IsHTML}
	{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML} -->

	{* Block 2 *}
	<!-- {option:block2IsHTML}{$block2}{/option:block2IsHTML}
	{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML} -->

	{* Block 3 *}
	<!-- {option:block3IsHTML}{$block3}{/option:block3IsHTML}
	{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML} -->


</body>
</html>