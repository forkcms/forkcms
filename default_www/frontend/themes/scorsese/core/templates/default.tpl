{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body class="{$LANGUAGE} frontend">

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
				<h2><a href="/">{$siteTitle}</a></h2>
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>
		
		<div id="main">
			<div id="topContent">
				{option:!hideContentTitle}<h1>{$page['title']}</h1>{/option:!hideContentTitle}
			</div>

			<div id="content">
				<div id="subnavigation">
					{$var|getsubnavigation:'page':{$page['id']}:1}
				</div>

				<div id="contentWrapper">
					<div class="content" id="mainContent">

						{* Block 1 *}
						{option:block1IsHTML}{$block1}{/option:block1IsHTML}
						{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
					</div>

					<div id="sideContent">
						<div class="sideBlock firstChild">
							{* Block 2 *}
							{option:block2IsHTML}{$block2}{/option:block2IsHTML}
							{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}
						</div>
						<div class="sideBlock">
							{* Block 3 *}
							{option:block3IsHTML}{$block3}{/option:block3IsHTML}
							{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}
						</div>
					</div>
				</div>

			</div>
		</div>





		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>