{include:core/layout/templates/head.tpl}

<body id="home">
	{include:core/layout/templates/header.tpl}

	<div class="holder" id="introHolder">
		<div id="intro" class="row">
			{* Intro left position *}
			{iteration:positionIntroLeft}
				{option:positionIntroLeft.blockIsHTML}
					<section class="col-4">
						{$positionIntroLeft.blockContent}
					</section>
				{/option:positionIntroLeft.blockIsHTML}
				{option:!positionIntroLeft.blockIsHTML}
					{$positionIntroLeft.blockContent}
				{/option:!positionIntroLeft.blockIsHTML}
			{/iteration:positionIntroLeft}

			{* Intro middle position *}
			{iteration:positionIntroMiddle}
				{option:positionIntroMiddle.blockIsHTML}
					<section class="col-4">
						{$positionIntroMiddle.blockContent}
					</section>
				{/option:positionIntroMiddle.blockIsHTML}
				{option:!positionIntroMiddle.blockIsHTML}
					{$positionIntroMiddle.blockContent}
				{/option:!positionIntroMiddle.blockIsHTML}
			{/iteration:positionIntroMiddle}

			{* Intro right position *}
			{iteration:positionIntroRight}
				{option:positionIntroRight.blockIsHTML}
					<section class="col-4">
						{$positionIntroRight.blockContent}
					</section>
				{/option:positionIntroRight.blockIsHTML}
				{option:!positionIntroRight.blockIsHTML}
					{$positionIntroRight.blockContent}
				{/option:!positionIntroRight.blockIsHTML}
			{/iteration:positionIntroRight}
		</div>
	</div>

	<div class="holder" id="contentHolder">
		<div id="content" class="row">
			{* Column 8 position *}
			{iteration:positionColumn8}
				<section class="col-8">
					{option:positionColumn8.blockIsHTML}
						{$positionColumn8.blockContent}
					{/option:positionColumn8.blockIsHTML}
					{option:!positionColumn8.blockIsHTML}
						{$positionColumn8.blockContent}
					{/option:!positionColumn8.blockIsHTML}
				</section>
			{/iteration:positionColumn8}

			{* Column 4 position *}
			{iteration:positionColumn4}
				<aside class="col-4">
					{option:positionColumn4.blockIsHTML}
						{$positionColumn4.blockContent}
					{/option:positionColumn4.blockIsHTML}
					{option:!positionColumn4.blockIsHTML}
						{$positionColumn4.blockContent}
					{/option:!positionColumn4.blockIsHTML}
				</aside>
			{/iteration:positionColumn4}
		</div>

		<div class="row">
			{* Column 6 position *}
			{iteration:positionColumn6}
				<section class="col-6">
					{option:positionColumn6.blockIsHTML}
						{$positionColumn6.blockContent}
					{/option:positionColumn6.blockIsHTML}
					{option:!positionColumn6.blockIsHTML}
						{$positionColumn6.blockContent}
					{/option:!positionColumn6.blockIsHTML}
				</section>
			{/iteration:positionColumn6}
		</div>

		<div class="row">
			{* Column 9 position *}
			{iteration:positionColumn9}
				<section class="col-9">
					{option:positionColumn9.blockIsHTML}
						{$positionColumn9.blockContent}
					{/option:positionColumn9.blockIsHTML}
					{option:!positionColumn9.blockIsHTML}
						{$positionColumn9.blockContent}
					{/option:!positionColumn9.blockIsHTML}
				</section>
			{/iteration:positionColumn9}

			{* Column 3 position *}
			{iteration:positionColumn3}
				<section class="col-3">
					{option:positionColumn3.blockIsHTML}
						{$positionColumn3.blockContent}
					{/option:positionColumn3.blockIsHTML}
					{option:!positionColumn3.blockIsHTML}
						{$positionColumn3.blockContent}
					{/option:!positionColumn3.blockIsHTML}
				</section>
			{/iteration:positionColumn3}
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