{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE}">
	<div id="container">

		<h2>this is the fork dev blog theme</h2>

		<p>Show me some blocks!</p>

		{* Block 0 *}
		{option:block0IsHTML}{$block0}{/option:block0IsHTML}
		{option:!block0IsHTML}{include:file='{$block0}'}{/option:!block0IsHTML}

		{* Block 1 *}
		{option:block1IsHTML}{$block1}{/option:block1IsHTML}
		{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}

		{* Block 2 *}
		{option:block2IsHTML}{$block2}{/option:block2IsHTML}
		{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}

	</div>
</body>
</html>