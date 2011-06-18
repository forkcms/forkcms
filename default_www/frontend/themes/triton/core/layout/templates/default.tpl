{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}">
	<div id="topWrapper">
		<header id="header">
			<div class="container">

				{* Logo *}
				<div id="logo">
					<h2><a href="/">{$siteTitle}</a></h2>
				</div>

				{* Skip link *}
				<div id="skip">
					<p><a href="#main">{$lblSkipToContent|ucfirst}</a></p>
				</div>

				{* Navigation *}
				<nav id="headerNavigation">
					<h4>{$lblMainNavigation|ucfirst}</h4>
					{$var|getnavigation:'page':0:1}
				</nav>

				{* Language *}
				<nav id="headerLanguage">
					<h4>{$lblLanguage|ucfirst}</h4>
					{include:core/layout/templates/languages.tpl}
				</nav>

				{* Block 10 (default: Search) *}
				{option:block10IsHTML}
					{option:block10}
						{$block10}
					{/option:block10}
				{/option:block10IsHTML}
				{option:!block10IsHTML}
					<div id="headerSearch">
						<h4>{$lblSearch|ucfirst}</h4>
						{$block10}
					</div>
				{/option:!block10IsHTML}

				{* Breadcrumb *}
				<div id="breadcrumb">
					<h4>{$lblBreadcrumb|ucfirst}</h4>
					{include:core/layout/templates/breadcrumb.tpl}
				</div>

				{* Block 9 (default: Editor) *}
				{option:block9IsHTML}
					{option:block9}
						<div id="headerAd">
							<h4>{$lblAdvertisement|ucfirst}</h4>
							{$block9}
						</div>
					{/option:block9}
				{/option:block9IsHTML}
				{option:!block9IsHTML}
					{$block9}
				{/option:!block9IsHTML}
			</div>

		</header>
		<div id="main">
			<div class="container">

				{* Left column *}
				<div class="col col-3">

					{* Subnavigation *}
					<nav class="sideNavigation">
						<h4>{$lblSubnavigation|ucfirst}</h4>
						{$var|getsubnavigation:'page':{$page.id}:2}
					</nav>

					{* Block 5 (default: Editor) *}
					{option:block5IsHTML}
						{option:block5}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block5}
									</div>
								</div>
							</section>
						{/option:block5}
					{/option:block5IsHTML}
					{option:!block5IsHTML}
						{$block5}
					{/option:!block5IsHTML}

					{* Block 6 (default: Editor) *}
					{option:block6IsHTML}
						{option:block6}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block6}
									</div>
								</div>
							</section>
						{/option:block6}
					{/option:block6IsHTML}
					{option:!block6IsHTML}
						{$block6}
					{/option:!block6IsHTML}

					{* Block 7 (default: Editor) *}
					{option:block7IsHTML}
						{option:block7}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block7}
									</div>
								</div>
							</section>
						{/option:block7}
					{/option:block7IsHTML}
					{option:!block7IsHTML}
						{$block7}
					{/option:!block7IsHTML}

					{* Block 8 (default: Editor) *}
					{option:block8IsHTML}
						{option:block8}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block8}
									</div>
								</div>
							</section>
						{/option:block8}
					{/option:block8IsHTML}
					{option:!block8IsHTML}
						{$block8}
					{/option:!block8IsHTML}

				</div>

				{* Right column *}
				<div class="col col-9 lastCol">

					{* Page title *}
					{option:!hideContentTitle}
						<header class="mainTitle">
							<h1>{$page.title}</h1>
						</header>
					{/option:!hideContentTitle}

					{* Block 1 (default: Editor) *}
					{option:block1IsHTML}
						{option:block1}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$block1}
									</div>
								</div>
							</section>
						{/option:block1}
					{/option:block1IsHTML}
					{option:!block1IsHTML}
						{$block1}
					{/option:!block1IsHTML}

					{* Block 2 (default: Editor) *}
					{option:block2IsHTML}
						{option:block2}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$block2}
									</div>
								</div>
							</section>
						{/option:block2}
					{/option:block2IsHTML}
					{option:!block2IsHTML}
						{$block2}
					{/option:!block2IsHTML}

					{* Block 3 (default: Editor) *}
					{option:block3IsHTML}
						{option:block3}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$block3}
									</div>
								</div>
							</section>
						{/option:block3}
					{/option:block3IsHTML}
					{option:!block3IsHTML}
						{$block3}
					{/option:!block3IsHTML}

					{* Block 4 (default: Editor) *}
					{option:block4IsHTML}
						{option:block4}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$block4}
									</div>
								</div>
							</section>
						{/option:block4}
					{/option:block4IsHTML}
					{option:!block4IsHTML}
						{$block4}
					{/option:!block4IsHTML}

				</div>
			</div>
		</div>
		<noscript>
			<div class="message notice">
				<h4>{$lblEnableJavascript|ucfirst}</h4>
				<p>{$msgEnableJavascript}</p>
			</div>
		</noscript>
	</div>
	<div id="bottomWrapper">
		{include:core/layout/templates/footer.tpl}
	</div>

	{* Site wide HTML *}
	{$siteHTMLFooter}

	{* General Javascript *}
	{iteration:javascriptFiles}
		<script src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

	{* Theme specific Javascript *}
	<script src="{$THEME_URL}/core/js/triton.js"></script>
</body>
</html>