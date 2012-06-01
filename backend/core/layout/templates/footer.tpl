	{include:{$BACKEND_CORE_PATH}/layout/templates/messaging.tpl}

	<div id="ajaxSpinner" style="position: fixed; top: 10px; right: 10px; display: none;">
		<img src="/backend/core/layout/images/spinner.gif" width="16" height="16" alt="loading" />
	</div>

	{iteration:jsFiles}<script src="{$jsFiles.file}"></script>{$CRLF}{$TAB}{/iteration:jsFiles}
	<script>
		//<![CDATA[
			{option:formError}jsBackend.messages.add('error', "{$errFormError|addslashes}");{/option:formError}

			{option:usingRevision}jsBackend.messages.add('notice', "{$msgUsingARevision|addslashes}");{/option:usingRevision}
			{option:usingDraft}jsBackend.messages.add('notice', "{$msgUsingADraft|addslashes}");{/option:usingDraft}

			{option:report}jsBackend.messages.add('success', "{$reportMessage|addslashes}");{/option:report}

			{option:errorMessage}jsBackend.messages.add('error', "{$errorMessage|addslashes}");{/option:errorMessage}
		//]]>
	</script>
</body>
</html>