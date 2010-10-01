{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body id="twocolumns" class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			<div id="language">
				{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">

			<div id="content">
				{include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'}
				{option:!hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:!hideContentTitle}

				{* Block 1 *}
				{option:block1IsHTML}
					<div class="content">
						{$block1}
					</div>
				{/option:block1IsHTML}
				{option:!block1IsHTML}
					{include:file='{$block1}'}
				{/option:!block1IsHTML}
				
				{* Block 2 *}
				{option:block2IsHTML}
					<div class="content">
						{$block2}
					</div>
				{/option:block2IsHTML}
				{option:!block2IsHTML}
					{include:file='{$block2}'}
				{/option:!block2IsHTML}
			</div>
			<div id="sidebar">


				{* Block 3 *}
				{option:block3IsHTML}
					<div class="content">
						{$block3}
					</div>
				{/option:block3IsHTML}
				{option:!block3IsHTML}
					{include:file='{$block3}'}
				{/option:!block3IsHTML}

				{* Block 4 *}
				{option:block4IsHTML}
					<div class="content">
						{$block4}
					</div>
				{/option:block4IsHTML}
				{option:!block4IsHTML}
					{include:file='{$block4}'}
				{/option:!block4IsHTML}

				{* Block 5 *}
				{option:block5IsHTML}
					<div class="content">
						{$block5}
					</div>
				{/option:block5IsHTML}
				{option:!block5IsHTML}
					{include:file='{$block5}'}
				{/option:!block5IsHTML}

				{* Block 6 *}
				{option:block6IsHTML}
					<div class="content">
						{$block6}
					</div>
				{/option:block6IsHTML}
				{option:!block6IsHTML}
					{include:file='{$block6}'}
				{/option:!block6IsHTML}

			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>