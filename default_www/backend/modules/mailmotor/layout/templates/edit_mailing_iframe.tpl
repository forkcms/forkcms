<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />

	<title>Fork 2.0.0 test - Fork CMS</title>
	<link rel="shortcut icon" href="/backend/favicon.ico" />

	<link rel="stylesheet" type="text/css" media="screen" href="/backend/modules/mailmotor/layout/css/iframe.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/imports/tinymce.css" />

	<script type="text/javascript" src="/backend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/backend/core/js/jquery/jquery.ui.js"></script>
	<script type="text/javascript" src="/backend/core/js/jquery/jquery.tools.js"></script>

	<script type="text/javascript" src="/backend/core/js/jquery/jquery.backend.js"></script>
	<script type="text/javascript" src="/backend/js.php?module=core&amp;file=backend.js&amp;language=nl&amp;m=1272467798"></script>
	<script type="text/javascript" src="/backend/js.php?module=core&amp;file=utils.js&amp;language=nl&amp;m=1272467798"></script>
	<script type="text/javascript" src="/backend/js.php?module=mailmotor&amp;file=mailmotor.js&amp;language=nl&amp;m=1272467798"></script>
	<script type="text/javascript" src="/backend/core/js/tiny_mce/jquery.tinymce.js"></script>
	<script type="text/javascript">
		//<![CDATA[
			var variables = new Array();
			variables =
			{
				mailingId: '{$mailing.id}',
				templateCSSPath: '{$template.path_css}',
				templateCSSURL: '{$template.url_css}'
			};

			function getTinyMCEContent()
			{
				// this cleans the tinyMCE and moves the current content to the textarea
				tinyMCE.triggerSave(false, true);

				// return the content
				return $('.inputEditor').tinymce().getContent();
			}
		//]]>
	</script>
	<script type="text/javascript" src="/backend/js.php?module=mailmotor&amp;file=tiny_mce_config.js&amp;language={$LANGUAGE}"></script>
</head>
<body id="content" class="edit addEdit">
	{$templateHtml}
</body>
</html>