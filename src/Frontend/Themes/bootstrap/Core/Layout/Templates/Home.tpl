{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">

	{include:Core/Layout/Templates/Header.tpl}

	{* Main position *}
	{iteration:positionMain}
		{option:positionMain.blockIsHTML}
		<div class="container">
			<div class="jumbotron">
				{$positionMain.blockContent}
				<p><a class="btn btn-lg btn-primary" href="#" role="button">{$lblMore|ucfirst} &raquo;</a></p>
			</div>
		</div>
		{/option:positionMain.blockIsHTML}
		{option:!positionMain.blockIsHTML}
			<div class="container">
				{$positionMain.blockContent}
			</div>
		{/option:!positionMain.blockIsHTML}
	{/iteration:positionMain}

	<div class="container">
		<div class="row">
			<div class="col-md-4">
				{* Left position *}
				{iteration:positionLeft}
					{$positionLeft.blockContent}
				{/iteration:positionLeft}
			</div>
			<div class="col-md-4">
				{* Middle position *}
				{iteration:positionMiddle}
					{$positionMiddle.blockContent}
				{/iteration:positionMiddle}
			</div>
			<div class="col-md-4">
				{* Right position *}
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