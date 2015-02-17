{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">

	{include:Core/Layout/Templates/Header.tpl}

    <div class="container">
		<div class="row">
            <div class="col-md-8">
				{* Main position *}
				{iteration:positionMain}
					{$positionMain.blockContent}
				{/iteration:positionMain}
			</div>

            <div class="col-md-4">
				{* Main position *}
				{iteration:positionRight}
					{$positionRight.blockContent}
				{/iteration:positionRight}
			</div>
		</div>
	</div>

	{include:Core/Layout/Templates/Footer.tpl}

	<noscript>
		<div class="message notice">
			<h4>{$lblEnableJavascript|ucfirst}</h4>
			<p>{$msgEnableJavascript}</p>
		</div>
	</noscript>	

	{* General Javascript *}
	{iteration:jsFiles}
		<script src="{$jsFiles.file}"></script>
	{/iteration:jsFiles}

	{* Theme specific Javascript *}
	<script src="{$THEME_URL}/Core/Js/boots.js"></script>
	<script src="{$THEME_URL}/Core/Js/bootstrap.min.js"></script>

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>