{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Tags &gt; {$msgHeaderEdit|sprintf:{$name}}</p>
			</div>

			<div class="inner">
				{form:edit}
					<div class="box">
						<div class="heading">
							<h3>Tag</h3>
						</div>
						<div class="options">
							{$txtName} {$txtNameError}
						</div>
					</div>

					<div class="fullwidthOptions">
						{option:deleteAllowed}
							<a href="{$var|geturl:'delete'}&id={$id}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
								<span><span><span>{$lblDelete|ucfirst}</span></span></span>
							</a>
							<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
								<p>
									<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
									{$msgConfirmDelete|sprintf:{$name}}
								</p>
							</div>
						{/option:deleteAllowed}
						<div class="buttonHolderRight">
							{$btnSave}
						</div>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}