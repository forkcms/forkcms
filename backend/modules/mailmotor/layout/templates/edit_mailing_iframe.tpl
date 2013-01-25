<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />

	<title>Fork CMS - mailing</title>
	<link rel="shortcut icon" href="/backend/favicon.ico" />

	<link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/backend/modules/mailmotor/layout/css/iframe.css" />

	<script type="text/javascript" src="/backend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/backend/core/js/jquery/jquery.ui.js"></script>
	<script type="text/javascript" src="/backend/core/js/jquery/jquery.ui.dialog.patch.js"></script>
	<script type="text/javascript" src="/backend/core/js/jquery/jquery.tools.js"></script>

	<script type="text/javascript" src="/backend/core/js/jquery/jquery.backend.js"></script>
	<script type="text/javascript" src="/backend/core/js/backend.js"></script>
	<script type="text/javascript" src="/backend/core/js/utils.js"></script>
	<script type="text/javascript" src="/backend/mailmotor/js/mailmotor.js"></script>
	<script type="text/javascript" src="/backend/core/js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="/backend/core/js/ckeditor/adapters/jquery.js"></script>
	<script type="text/javascript" src="/backend/core/js/ckfinder/ckfinder.js"></script>
	<script type="text/javascript" src="/frontend/cache/navigation/editor_link_list_{$LANGUAGE}.js"></script>
	<script type="text/javascript">
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

			var variables = new Array();
			variables =
			{
				mailingId: '{$mailing.id}',
				templateCSSPath: '{$template.path_css}',
				templateCSSURL: '{$template.url_css}'
			};

			// we need this method so we can easily access the editor's contents outside the iframe.
			function getEditorContent()
			{
				return $('.inputEditorNewsletter').val();
			}
		//]]>
	</script>
</head>
<body id="content" class="edit addEdit">
	{$templateHtml}
</body>
</html>
