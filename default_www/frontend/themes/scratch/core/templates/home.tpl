{include:{$THEME_PATH}/core/templates/head.tpl}

<body id="home" class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h2><a href="/">{$siteTitle}</a></h2>
			<div id="language">
				{include:{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl}
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">
			<div id="intro">

				{* Block 1 (default: Editor) *}
				{option:block1IsHTML}
					{option:block1}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block1}
								</div>
							</div>
						</div>
					{/option:block1}
				{/option:block1IsHTML}
				{option:!block1IsHTML}
					{include:{$block1}}
				{/option:!block1IsHTML}
			</div>
			<div id="content">

				{* Block 2 (default: Editor) *}
				{option:block2IsHTML}
					{option:block2}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block2}
								</div>
							</div>
						</div>
					{/option:block2}
				{/option:block2IsHTML}
				{option:!block2IsHTML}
					{include:{$block2}}
				{/option:!block2IsHTML}

				{* Block 3 (default: Recent articles) *}
				{option:block3IsHTML}
					{option:block3}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block3}
								</div>
							</div>
						</div>
					{/option:block3}
				{/option:block3IsHTML}
				{option:!block3IsHTML}
					{include:{$block3}}
				{/option:!block3IsHTML}
			</div>
		</div>

		<div id="footer">
			{include:{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl}
		</div>
	</div>
</body>
</html>