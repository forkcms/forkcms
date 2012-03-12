{include:core/layout/templates/head.tpl}

<body>

	{include:core/layout/templates/header.tpl}

<div class="holder" id="contentHolder">
	<div id="content" class="row clearfix">

		{* Main *}

		{option:positionMain}
		<section class="col-8">
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
				{iteration:positionRight}
				{option:!positionRight.blockIsHTML}
					{$positionRight.blockContent}
				{/option:!positionRight.blockIsHTML}
				{option:positionRight.blockIsHTML}
					{$positionRight.blockContent}
				{/option:positionRight.blockIsHTML}
				{/iteration:positionRight}
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