				{include:file='{$BACKEND_CORE_PATH}/layout/templates/messaging.tpl'}
				</div>
			<td>
		</tr>
	</table>

	<script type="text/javascript">
		{option:formError}jsBackend.messages.add('error', "{$errFormError}");{/option:formError}
		{option:usingRevision}jsBackend.messages.add('notice', "{$msgUsingARevision}");{/option:usingRevision}
		{option:report}jsBackend.messages.add('success', "{$reportMessage}");{/option:report}
	</script>

</body>
</html>