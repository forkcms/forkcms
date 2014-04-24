<!DOCTYPE html>
<html lang="{$INTERFACE_LANGUAGE}">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<meta name="robots" content="noindex, nofollow" />

	<title>{$SITE_TITLE} - Fork CMS</title>
	<link rel="shortcut icon" href="/src/Backend/favicon.ico" />

	{iteration:cssFiles}<link rel="stylesheet" href="{$cssFiles.file}" />{$CRLF}{$TAB}{/iteration:cssFiles}
	<!--[if IE 7]><link rel="stylesheet" href="/src/Backend/Core/Layout/Css/conditionals/ie7.css" /><![endif]-->
	<!--[if IE 8]><link rel="stylesheet" href="/src/Backend/Core/Layout/Css/conditionals/ie8.css" /><![endif]-->

	{iteration:jsFiles}<script src="{$jsFiles.file}"></script>{$CRLF}{$TAB}{/iteration:jsFiles}
	<script>
		//<![CDATA[
			{$jsData}

			// reports
			$(function()
			{
				{option:formError}jsBackend.messages.add('error', "{$errFormError|addslashes}");{/option:formError}
				{option:usingRevision}jsBackend.messages.add('notice', "{$msgUsingARevision|addslashes}");{/option:usingRevision}
				{option:usingDraft}jsBackend.messages.add('notice', "{$msgUsingADraft|addslashes}");{/option:usingDraft}
				{option:report}jsBackend.messages.add('success', "{$reportMessage|addslashes}");{/option:report}
				{option:errorMessage}jsBackend.messages.add('error', "{$errorMessage|addslashes}");{/option:errorMessage}
			});
		//]]>
	</script>
</head>
