<div class="holder" id="headerholder">
	<header id="header" class="row">
		<div class="col-12">
			{option:page1}<h1><a class="logo" href="/">{$siteTitle}</a></h1>{/option:page1}
			{option:!page1}<p><a class="logo" href="/">{$siteTitle}</a></p>{/option:!page1}
		</div>
	</header>
</div>

<div class="holder" id="navholder">
	<nav id="navigation" class="row clearfix">
		{$var|getnavigation:'page':0:1}
	</nav>
</div>