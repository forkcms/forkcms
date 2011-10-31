{include:core/layout/templates/head.tpl}

<body id="home">
	{include:core/layout/templates/header.tpl}

	<div class="holder" id="introHolder">
		<div id="intro" class="row">
			{* Left position *}
			{iteration:positionLeft}
				{option:positionLeft.blockIsHTML}
					<section class="col-4">
						{$positionLeft.blockContent}
					</section>
				{/option:positionLeft.blockIsHTML}
				{option:!positionLeft.blockIsHTML}
					{$positionLeft.blockContent}
				{/option:!positionLeft.blockIsHTML}
			{/iteration:positionLeft}

			{* Main position *}
			{iteration:positionMain}
				{option:positionMain.blockIsHTML}
					<section class="col-4">
						{$positionMain.blockContent}
					</section>
				{/option:positionMain.blockIsHTML}
				{option:!positionMain.blockIsHTML}
					{$positionMain.blockContent}
				{/option:!positionMain.blockIsHTML}
			{/iteration:positionMain}

			{* Right position *}
			{iteration:positionRight}
				{option:positionRight.blockIsHTML}
					<section class="col-4">
						{$positionRight.blockContent}
					</section>
				{/option:positionRight.blockIsHTML}
				{option:!positionRight.blockIsHTML}
					{$positionRight.blockContent}
				{/option:!positionRight.blockIsHTML}
			{/iteration:positionRight}
		</div>
	</div>

	<div class="holder" id="contentHolder">
		<div id="content" class="row">
			{* Top left position *}
			{iteration:positionTopLeft}
				<section class="col-8">
					{option:positionTopLeft.blockIsHTML}
						{$positionTopLeft.blockContent}
					{/option:positionTopLeft.blockIsHTML}
					{option:!positionTopLeft.blockIsHTML}
						{$positionTopLeft.blockContent}
					{/option:!positionTopLeft.blockIsHTML}
				</section>
			{/iteration:positionTopLeft}

			{* Top right position *}
			{iteration:positionTopRight}
				<aside class="col-4">
					{option:positionTopRight.blockIsHTML}
						{$positionTopRight.blockContent}
					{/option:positionTopRight.blockIsHTML}
					{option:!positionTopRight.blockIsHTML}
						{$positionTopRight.blockContent}
					{/option:!positionTopRight.blockIsHTML}
				</aside>
			{/iteration:positionTopRight}
		</div>

		<div class="row">
			{* Wide position *}
			{iteration:positionWide}
				<section class="col-6">
					{option:positionWide.blockIsHTML}
						{$positionWide.blockContent}
					{/option:positionWide.blockIsHTML}
					{option:!positionWide.blockIsHTML}
						{$positionWide.blockContent}
					{/option:!positionWide.blockIsHTML}
				</section>
			{/iteration:positionWide}
		</div>

		<div class="row">
			{* Bottom left position *}
			{iteration:positionBottomLeft}
				<section class="col-9">
					{option:positionBottomLeft.blockIsHTML}
						{$positionBottomLeft.blockContent}
					{/option:positionBottomLeft.blockIsHTML}
					{option:!positionBottomLeft.blockIsHTML}
						{$positionBottomLeft.blockContent}
					{/option:!positionBottomLeft.blockIsHTML}
				</section>
			{/iteration:positionBottomLeft}

			{* Bottom right position *}
			{iteration:positionBottomRight}
				<section class="col-3">
					{option:positionBottomRight.blockIsHTML}
						{$positionBottomRight.blockContent}
					{/option:positionBottomRight.blockIsHTML}
					{option:!positionBottomRight.blockIsHTML}
						{$positionBottomRight.blockContent}
					{/option:!positionBottomRight.blockIsHTML}
				</section>
			{/iteration:positionBottomRight}
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
	{iteration:jsFiles}
		<script src="{$jsFiles.file}"></script>
	{/iteration:jsFiles}

	<script src="{$THEME_URL}/core/js/respond.min.js"></script>

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>