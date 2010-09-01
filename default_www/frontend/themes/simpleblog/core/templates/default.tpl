{include:file='{$THEME_PATH}/core/templates/_head.tpl'}
<body id="home" class="{$LANGUAGE}">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>
		<div id="main" class="clearfix">
			<div id="content">

				{option:page1}{option:hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:hideContentTitle}{/option:page1}
				{option:!page1}{option:!hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:!hideContentTitle}{/option:!page1}

				{option:block1IsHTML}{$block1}{/option:block1IsHTML}
				{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}

				{option:block2IsHTML}{$block2}{/option:block2IsHTML}
				{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}

			</div>
			<div id="sidebar">

				{option:block3IsHTML}{$block3}{/option:block3IsHTML}
				{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}

				{option:block4IsHTML}{$block4}{/option:block4IsHTML}
				{option:!block4IsHTML}{include:file='{$block4}'}{/option:!block4IsHTML}

				{option:block5IsHTML}{$block5}{/option:block5IsHTML}
				{option:!block5IsHTML}{include:file='{$block5}'}{/option:!block5IsHTML}

				{option:block6IsHTML}{$block6}{/option:block6IsHTML}
				{option:!block6IsHTML}{include:file='{$block6}'}{/option:!block6IsHTML}

				{option:block7IsHTML}{$block7}{/option:block7IsHTML}
				{option:!block7IsHTML}{include:file='{$block7}'}{/option:!block7IsHTML}

			</div>
		</div>

		{include:file='{$THEME_PATH}/core/templates/_footer.tpl'}

	</div>
</body>
</html>