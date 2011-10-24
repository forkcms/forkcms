{include:core/layout/templates/head.tpl}

<body id="home">
	{include:core/layout/templates/header.tpl}

	<div class="holder" id="contentHolder">
		<div id="content" class="row">
			{option:!hideContentTitle}
				<header class="col-12">
					<h1>{$page.title}</h1>
				</header>
			{/option:!hideContentTitle}

			<section class="col-8">
				{* Column left position *}
				{iteration:positionColumnLeft}
					{option:positionColumnLeft.blockIsHTML}
						{$positionColumnLeft.blockContent}
					{/option:positionColumnLeft.blockIsHTML}
					{option:!positionColumnLeft.blockIsHTML}
						{$positionColumnLeft.blockContent}
					{/option:!positionColumnLeft.blockIsHTML}
				{/iteration:positionColumnLeft}
			</section>

			<aside class="col-4">
				{* Column right position *}
				{iteration:positionColumnRight}
					{option:positionColumnRight.blockIsHTML}
						{$positionColumnRight.blockContent}
					{/option:positionColumnRight.blockIsHTML}
					{option:!positionColumnRight.blockIsHTML}
						{$positionColumnRight.blockContent}
					{/option:!positionColumnRight.blockIsHTML}
				{/iteration:positionColumnRight}
			</aside>
		</div>

		<noscript>
			<div class="message notice">
				<h4>{$lblEnableJavascript|ucfirst}</h4>
				<p>{$msgEnableJavascript}</p>
			</div>
		</noscript>
	</div>

	<div class="holder" id="doormatHolder">
		{include:core/layout/templates/doormat.tpl}
	</div>

	<div class="holder" id="footerHolder">
		{include:core/layout/templates/footer.tpl}
	</div>

	{* General Javascript *}
	{iteration:javascriptFiles}
		<script src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

	<script src="{$THEME_URL}/core/js/respond.min.js"></script>

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>