{include:file='{$THEME_PATH}/core/templates/head.tpl'}

<body id="home" class="{$LANGUAGE}">

	<div id="container">
		<div id="top">

			<div id="metaNavigation">
				{$var|getnavigation:'meta':0:1}
			</div>

			{include:file='{$THEME_PATH}/core/templates/languages.tpl'}

			<form action="#" method="get">
				<fieldset>
					<input type="text" name="q" id="q" class="inputText" value="" />
					<input type="submit" name="search" value="Search" class="inputSubmit" />
				</fieldset>
			</form>
		</div>

		<div id="header">
			<div id="logo">
				<h1><a href="/">{$siteTitle}</a></h1>
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">
			<div id="intro">
				<div class="imageWrapper floatLeft">
					{* Block 1 *}
					{option:block1IsHTML}{$block1}{/option:block1IsHTML}
					{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
				</div>
				<div class="content">
					{* Block 2 *}
					{option:block2IsHTML}{$block2}{/option:block2IsHTML}
					{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}
				</div>
			</div>

			<div id="content">
				<div id="contentWrapper">
					<div id="mainContent">
						{* Block 3 *}
						{option:block3IsHTML}{$block3}{/option:block3IsHTML}
						{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}
					</div>

					<div id="sideContent">
						<div class="sideBlock firstChild">
							{* Block 4 *}
							{option:block4IsHTML}{$block4}{/option:block4IsHTML}
							{option:!block4IsHTML}{include:file='{$block4}'}{/option:!block4IsHTML}
						</div>
						<div class="sideBlock">
							{* Block 5 *}
							{option:block5IsHTML}{$block5}{/option:block5IsHTML}
							{option:!block5IsHTML}{include:file='{$block5}'}{/option:!block5IsHTML}
						</div>
					</div>
				</div>

			</div>
		</div>
		
	</div>

	{include:file='{$THEME_PATH}/core/templates/footer.tpl'}

</body>
</html>