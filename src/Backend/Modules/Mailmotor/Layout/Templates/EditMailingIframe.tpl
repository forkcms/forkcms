<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
  <meta http-equiv="X-UA-Compatible" content="chrome=1" />

  <title>Fork CMS - mailing</title>
  <link rel="shortcut icon" href="/Backend/favicon.ico" />

  {iteration:cssFiles}
  <link rel="stylesheet" href="{$cssFiles.file}" />{$CRLF}{$TAB}
  {/iteration:cssFiles}

  {iteration:jsFiles}
  <script src="{$jsFiles.file}"></script>{$CRLF}{$TAB}
  {/iteration:jsFiles}

  <script type="text/javascript">
    //<![CDATA[
      {$jsData}

      // reports
      $(function()
      {
        {option:formError}jsBackend.messages.add('danger', "{$errFormError|addslashes}");{/option:formError}
        {option:usingRevision}jsBackend.messages.add('notice', "{$msgUsingARevision|addslashes}");{/option:usingRevision}
        {option:usingDraft}jsBackend.messages.add('notice', "{$msgUsingADraft|addslashes}");{/option:usingDraft}
        {option:report}jsBackend.messages.add('success', "{$reportMessage|addslashes}");{/option:report}
        {option:errorMessage}jsBackend.messages.add('danger', "{$errorMessage|addslashes}");{/option:errorMessage}
      });

      var variables = [];
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
