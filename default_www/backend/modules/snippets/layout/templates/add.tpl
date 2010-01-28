{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
	<td id="contentHolder">
			<div id="statusBar">
				<!-- @todo add label -->
				<!-- @todo breadcrumb should be generated -->
				<p class="breadcrumb">Snippets > {$msgHeaderAdd}</p>
			</div>
			<div class="inner">

				{form:add}
				<fieldset>
					<div class="box">
						<div class="heading">
							<h3>{$msgHeaderAdd}</h3>
						</div>
						<div class="options">
						
							<p>
								<label for="title">{$lblTitle|ucfirst}</label>
								{$txtTitle} {$txtTitleError}
							</p>
							
							<p>
								<label for="content">{$lblContent|ucfirst}</label>
								{$txtContent} {$txtContentError}
							</p>

							<p>
								<label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label>
							</p>

							<div class="fullwidthOptions">
								<div class="buttonHolderRight">
									{$btnSave}
								</div>
							</div>
						</div>
					</div>
				</fieldset>
				{/form:add}
				

			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}







