				{include:file='{$BACKEND_CORE_PATH}/layout/templates/messaging.tpl'}
				</div>
			</td>
		</tr>
	</table>

	<script type="text/javascript">
		//<![CDATA[
			{option:formError}jsBackend.messages.add('error', "{$errFormError}");{/option:formError}

			{option:usingRevision}jsBackend.messages.add('notice', "{$msgUsingARevision}");{/option:usingRevision}
			{option:usingDraft}jsBackend.messages.add('notice', "{$msgUsingADraft}");{/option:usingDraft}

			{option:report}jsBackend.messages.add('success', "{$reportMessage}");{/option:report}

			{option:errorMessage}jsBackend.messages.add('error', "{$errorMessage}");{/option:errorMessage}
		//]]>
	</script>
</body>
</html>