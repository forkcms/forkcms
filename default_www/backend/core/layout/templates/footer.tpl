{include:file='{$BACKEND_CORE_PATH}/layout/templates/messaging.tpl'}

<script type="text/javascript">
	//<![CDATA[
		{option:formError}jsBackend.messages.add('error', "{$errFormError|addslashes}");{/option:formError}

		{option:usingRevision}jsBackend.messages.add('notice', "{$msgUsingARevision|addslashes}");{/option:usingRevision}
		{option:usingDraft}jsBackend.messages.add('notice', "{$|addslashes}");{/option:usingDraft}

		{option:report}jsBackend.messages.add('success', "{$reportMessage|addslashes}");{/option:report}

		{option:errorMessage}jsBackend.messages.add('error', "{$errorMessage|addslashes}");{/option:errorMessage}
	//]]>
</script>

</body>
</html>