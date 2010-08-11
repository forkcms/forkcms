{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body class="{$LANGUAGE} frontend">

	<div id="container">
		<div id="top">

			<div id="metaNavigation">
				{$var|getnavigation:'meta':0:1}
			</div>

			{include:file='{$THEME_PATH}/core/templates/languages.tpl'}

			{* Block 5: search *}
			{option:block5IsHTML}{$block5}{/option:block5IsHTML}
			{option:!block5IsHTML}{include:file='{$block5}'}{/option:!block5IsHTML}
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

						{* Block 2 *}
						{option:block2IsHTML}
						<div class="sideBlock firstChild">
							{$block2}
						</div>
						{/option:block2IsHTML}
						{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}

						{* Block 3 *}
						{option:block3IsHTML}
						<div class="sideBlock">
							{$block3}
						</div>
						{/option:block3IsHTML}
						{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}

						{* Block 4 *}
						{option:block4IsHTML}
						<div class="sideBlock">
							{$block4}
						</div>
						{/option:block4IsHTML}
						{option:!block4IsHTML}{include:file='{$block4}'}{/option:!block4IsHTML}

					</div>
				</div>

			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>