{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE}">
	<div id="container">

		<div id="sidebar">
			<h1><a href="/">{$siteTitle}</a></h1>

			{* Call to action 1 *}
			{option:block1IsHTML}{$block1}{/option:block1IsHTML}
			{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}

			{* Call to action 2 *}
			{option:block3IsHTML}{$block3}{/option:block3IsHTML}
			{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}
			
			<form action="#" id="newsletterSubscribeForm" method="post" class="forkForms">
				<p class="p0"><input type="text" class="inputText" /></p>
				<p><a class="button" href="#">Inschrijven</a></p>
			</form>
			<p>Alternatively, subscribe to the <a href="#">RSS feed</a>.</p>

		</div>

		<div id="content">
			{* Blog *}
			{option:block2IsHTML}{$block2}{/option:block2IsHTML}
			{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}
		</div>

	</div>
</body>
</html>