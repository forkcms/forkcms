<!DOCTYPE html>
<html lang="{$INTERFACE_LANGUAGE}">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="chrome=1" />
  <meta name="robots" content="noindex, nofollow" />

  <title>{option:page_title}{$page_title|ucfirst} - {/option:page_title}{$SITE_TITLE} - Fork CMS</title>
  <link rel="shortcut icon" href="/src/Backend/favicon.ico" />

  <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>

  {iteration:cssFiles}
  <link rel="stylesheet" href="{$cssFiles.file}" />{$CRLF}{$TAB}
  {/iteration:cssFiles}

  {iteration:jsFiles}
  <script src="{$jsFiles.file}"></script>{$CRLF}{$TAB}
  {/iteration:jsFiles}

  <script>
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
    //]]>
  </script>
</head>
