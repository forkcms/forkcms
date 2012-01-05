{include:core/layout/templates/head.tpl}

<body>
<div class="holder" id="headerHolder">
	<header id="header" class="row">
		<div class="col-12">
			<h1 class="logo"><a href="/" title="{$siteTitle}">{$siteTitle}</a></h1>
			<nav id="navigation" class="clearfix">
				{* $var|getnavigation:'page':0:2 *}
			</nav>
			{* Top (default: search widget) *}
			{iteration:positionTop}
				{option:!positionTop.blockIsHTML}
				{$positionTop.blockContent}
				{/option:!positionTop.blockIsHTML}
			{/iteration:positionTop}
		</div>
	</header>
</div>

<div class="holder" id="contentHolder"> 
	<div id="content" class="row">
		{* Main *}
		{option:positionMain}
		<section class="col-8 content">
			{iteration:positionMain}
			{option:!positionMain.blockIsHTML}
				{$positionMain.blockContent}
			{/option:!positionMain.blockIsHTML}
			{option:positionMain.blockIsHTML}
				{$positionMain.blockContent}
			{/option:positionMain.blockIsHTML}
			{/iteration:positionMain}
		</section>
		{/option:positionMain}

		{* Right *}

		{option:positionRight}
		<section id="sidebar" class="col-4">
			<div class="box">
			{iteration:positionRight}
			{option:!positionRight.blockIsHTML}
			<div class="before1 c2">
				{$positionRight.blockContent}
			</div>
			{/option:!positionRight.blockIsHTML}
			{option:positionRight.blockIsHTML}
			<aside class="page-aside c3">
				{$positionRight.blockContent}
			</aside>
			{/option:positionRight.blockIsHTML}
			{/iteration:positionRight}
			</div>
		</section>
		{/option:positionRight}

	</div>
</div>
{include:core/layout/templates/footer.tpl}

{* General Javascript *}
{iteration:jsFiles}
<script src="{$jsFiles.file}"></script>
{/iteration:jsFiles}
<script src="{$THEME_URL}/core/layout/js/scripts.js"></script>
</body>
</html>