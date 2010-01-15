{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Blog &gt; {$msgHeaderComments}</p>
			</div>

			<div class="inner">
				{option:report}
					<div class="report fadeOutAfterMouseMove">{$reportMessage}</div>
					{option:highlight}
						<script type="text/javascript">
							var highlightId = '#{$highlight}';
						</script>
					{/option:highlight}
				{/option:report}

				<div id="tabs" class="tabs">
					<ul>
						<li><a href="#tabPublished">{$lblPublishedComments|ucfirst} ({$numPublished})</a></li>
						<li><a href="#tabModeration">{$lblWaitingForModeration|ucfirst} ({$numModeration})</a></li>
						<li><a href="#tabSpam">{$lblSpam|ucfirst} ({$numSpam})</a></li>
					</ul>

					<div id="tabPublished">
						{option:dgPublished}
							<form action="{$var|geturl:'comment_status'}" method="get" class="forkForms submitWithLink" id="commentsPublished">
								<input type="hidden" name="from" value="published" />
								{$dgPublished}
							</form>
						{/option:dgPublished}
						{option:!dgPublished}{$msgNoItems}{/option:!dgPublished}
					</div>

					<div id="tabModeration">
						{option:dgModeration}{$dgModeration}{/option:dgModeration}
						{option:!dgModeration}{$msgNoItems}{/option:!dgModeration}
					</div>

					<div id="tabSpam">
						{option:dgSpam}{$dgSpam}{/option:dgSpam}
						{option:!dgSpam}{$msgNoItems}{/option:!dgSpam}
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}