{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE}">
	<div id="container">

		<!-- {$var|getnavigation} -->

		<div id="sidebar">
			<h1><a href="/">{$siteTitle}</a></h1>

			<div class="contentBlocks">
					{* Block 1 *}
					{option:block1IsHTML}{$block1}{/option:block1IsHTML}
					{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
				</div>
				<div id="newsletterSubscribe" class="contentBlock">
					{* Block 2 *}
					{option:block2IsHTML}{$block2}{/option:block2IsHTML}
					{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}

					{* @todo *}
					<form action="home_submit" method="post" class="forkForms">
						<p><input type="text" class="inputText" /></p>
						<p><a class="button" href="#">{$lblSubscribe|ucfirst}</a></p>
					</form>
					<p>Alternatively, subscribe to the <a href="#">RSS feed</a>.</p>
				</div>
			</div>
		</div>

		<div id="content">
			{* Block 0 *}
			{option:block0IsHTML}{$block0}{/option:block0IsHTML}
			{option:!block0IsHTML}{include:file='{$block0}'}{/option:!block0IsHTML}
		</div>
	</div>
</body>
</html>