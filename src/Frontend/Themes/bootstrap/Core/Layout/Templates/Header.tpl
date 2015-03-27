{include:Core/Layout/Templates/CookieBar.tpl}

<div class="navbar navbar-inverse" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<!--@ToDo label Toggle Navigation -->
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">{$siteTitle}</a>
		</div>
		<div class="navbar-collapse collapse">
			{* Navigation *}
			<nav id="headerNavigation">
				<h4>{$lblMainNavigation|ucfirst}</h4>
				<ul class="nav navbar-nav">
				{$var|getnavigation:'page':0:2}
			</nav>

			{* Language *}
			<nav id="headerLanguage">
				<h4>{$lblLanguage|ucfirst}</h4>
				{include:Core/Layout/Templates/Languages.tpl}
			</nav>

			{* Search position *}
			{iteration:positionSearch}
				{option:!positionSearch.blockIsHTML}
					{$positionSearch.blockContent}
				{/option:!positionSearch.blockIsHTML}
			{/iteration:positionSearch}

		</div>
	</div>
</div>

{* Breadcrumb *}
<div id="breadcrumb">
	<div class="container">
		<h4>{$lblBreadcrumb|ucfirst}</h4>
		{include:Core/Layout/Templates/Breadcrumb.tpl}
	</div>
</div>
