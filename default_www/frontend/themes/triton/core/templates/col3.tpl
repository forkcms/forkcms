{include:'{$THEME_PATH}/core/templates/head.tpl'}

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
					<p><a href="#main">{$msgSkipToContent}</a></p>
				</div>

				{* Navigation *}
				<nav id="headerNavigation">
					<h4>{$lblMainNavigation}</h4>
					{$var|getnavigation:'page':0:1}
				</nav>

				{* Language *}
				<aside id="headerLanguage">
					<h4>{$lblLanguage}</h4>
					{include:{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl}
				</aside>

				{* Block 14 (default: Search) *}
				{option:block14IsHTML}
					{option:block14}
						{$block14}
					{/option:block14}
				{/option:block14IsHTML}
				{option:!block14IsHTML}
					<aside id="headerSearch">
						<h4>{$lblSearch}</h4>
						{include:{$block14}}
					</aside>
				{/option:!block14IsHTML}

				{* Breadcrumb *}
				<aside id="breadcrumb">
					<h4>{$lblBreadcrumb}</h4>
					{include:{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl}
				</aside>

				{* Block 13 (default: Editor) *}
				{option:block13IsHTML}
					{option:block13}
						<aside id="headerAd">
							<h4>{$lblAdvertisement}</h4>
							{$block13}
						</aside>
					{/option:block13}
				{/option:block13IsHTML}
				{option:!block13IsHTML}
					{include:{$block13}}
				{/option:!block13IsHTML}
			</div>

		</header>
		<div id="main">
			<div class="container">

				{* Left column *}
				<div class="col col-3">

					{* Subnavigation *}
					<nav class="sideNavigation">
						<h4>{$lblSubnavigation}</h4>
						{$var|getsubnavigation:'page':{$page.id}:2}
					</nav>

					{* Block 9 (default: Editor) *}
					{option:block9IsHTML}
						{option:block9}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block9}
									</div>
								</div>
							</section>
						{/option:block9}
					{/option:block9IsHTML}
					{option:!block9IsHTML}
						{include:{$block9}}
					{/option:!block9IsHTML}

					{* Block 10 (default: Editor) *}
					{option:block10IsHTML}
						{option:block10}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block10}
									</div>
								</div>
							</section>
						{/option:block10}
					{/option:block10IsHTML}
					{option:!block10IsHTML}
						{include:{$block10}}
					{/option:!block10IsHTML}

					{* Block 11 (default: Editor) *}
					{option:block11IsHTML}
						{option:block11}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block11}
									</div>
								</div>
							</section>
						{/option:block11}
					{/option:block11IsHTML}
					{option:!block11IsHTML}
						{include:{$block11}}
					{/option:!block11IsHTML}

					{* Block 12 (default: Editor) *}
					{option:block12IsHTML}
						{option:block12}
							<section class="mod widget">
								<div class="inner">
									<div class="bd content">
										{$block12}
									</div>
								</div>
							</section>
						{/option:block12}
					{/option:block12IsHTML}
					{option:!block12IsHTML}
						{include:{$block12}}
					{/option:!block12IsHTML}

				</div>

				{* Middle column *}
				<div class="col col-6">

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
						{include:{$block1}}
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
						{include:{$block2}}
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
						{include:{$block3}}
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
						{include:{$block4}}
					{/option:!block4IsHTML}

				</div>
				
				{* Right column *}
				<div class="col col-3 lastCol">

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
						{include:{$block5}}
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
						{include:{$block6}}
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
						{include:{$block7}}
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
						{include:{$block8}}
					{/option:!block8IsHTML}

				</div>
			</div>
		</div>
		<noscript>
			<div class="message notice">
				<h4>{$lblEnableJavascript}</h4>
				<p>{$msgEnableJavascript}</p>
			</div>
		</noscript>
	</div>
	<div id="bottomWrapper">
		{include:{$THEME_PATH}/core/templates/footer.tpl}
	</div>
</body>
</html>