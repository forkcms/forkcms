{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblBlog|ucfirst} &gt; {$lblComments|ucfirst}</p>
			</div>

			<div class="inner">
				<div id="tabs" class="tabs">
					<ul>
						<li><a href="#tabPublished">{$lblPublishedComments|ucfirst} ({$numPublished})</a></li>
						<li><a href="#tabModeration">{$lblWaitingForModeration|ucfirst} ({$numModeration})</a></li>
						<li><a href="#tabSpam">{$lblSpam|ucfirst} ({$numSpam})</a></li>
					</ul>

					<div id="tabPublished">
						{option:dgPublished}
							<form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms submitWithLink" id="commentsPublished">
								<input type="hidden" name="from" value="published" />
								<div class="datagridHolder">
									{$dgPublished}
								</div>
							</form>
						{/option:dgPublished}
						{option:!dgPublished}{$msgNoItems}{/option:!dgPublished}
					</div>

					<div id="tabModeration">
						{option:dgModeration}
							<form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms submitWithLink" id="commentsModeration">
								<input type="hidden" name="from" value="moderation" />
								<div class="datagridHolder">
									{$dgModeration}
								</div>
							</form>
						{/option:dgModeration}
						{option:!dgModeration}{$msgNoItems}{/option:!dgModeration}
					</div>

					<div id="tabSpam">
						{option:dgSpam}
							<form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms submitWithLink" id="commentsSpam">
								<input type="hidden" name="from" value="spam" />
								<div class="datagridHolder">
									{$dgSpam}
								</div>
							</form>
						{/option:dgSpam}
						{option:!dgSpam}{$msgNoItems}{/option:!dgSpam}
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}