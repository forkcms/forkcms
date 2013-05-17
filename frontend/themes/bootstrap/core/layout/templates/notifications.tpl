<!-- warning for people that don't have JS enabled -->
<noscript>
	<div class="fullWidthAlert alert">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<strong>{$lblWarning|ucfirst}:</strong> {$msgEnableJavascript}
	</div>
</noscript>

<!-- Warning for people that still use IE7 or below -->
<!--[if lt IE 8 ]>
<div id="ie" class="fullWidthAlert alert">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{$lblWarning|ucfirst}:</strong> {$msgOldBrowser}
</div>
<![endif]-->

<a href="#main" class="muted hide">{$lblSkipToContent|ucfirst}</a>

{option:!cookieBarHide}
	<div id="cookieBar" class="fullWidthAlert alert">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<strong>{$lblWarning|ucfirst}:</strong> {$msgCookies}

		<a href="#" id="cookieBarAgree" class="btn btn-mini">{$lblIAgree|ucfirst}</a>
		<a href="#" id="cookieBarDisagree" class="btn btn-mini">{$lblIDisagree|ucfirst}</a>
	</div>
{/option:!cookieBarHide}

