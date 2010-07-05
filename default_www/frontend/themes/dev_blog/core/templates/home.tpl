{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE}">
	<div id="container">

		<!-- {$var|getnavigation} -->

		<div id="sidebar">
			<h1><a href="/">{$siteTitle}</a></h1>

			<div class="contentBlocks">
					{* Block 2 *}
					{option:block2IsHTML}{$block2}{/option:block2IsHTML}
					{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}
				</div>
				<div id="newsletterSubscribe" class="contentBlock">
					{* Block 3 *}
					{option:block3IsHTML}{$block3}{/option:block3IsHTML}
					{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}

					{* @todo ... No mailmotor for now.
					<form action="home_submit" method="post" class="forkForms">
						<p><input type="text" class="inputText" /></p>
						<p><a class="button" href="#">{$lblSubscribe|ucfirst}</a></p>
					</form>
					<p>Alternatively, subscribe to the <a href="#">RSS feed</a>.</p>
					*}
				</div>
			</div>
		</div>

		<div id="content">
			{* Block 1 *}
			{option:block1IsHTML}{$block1}{/option:block1IsHTML}
			{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
		</div>
	</div>
</body>
</html>