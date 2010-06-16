{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				{form:add}
					<div class="box">
						<div class="heading">
							<h3>{$lblContentBlocks|ucfirst}: {$lblAdd}</h3>
						</div>
						<div class="content">
							<fieldset>
								<p>
									<label for="title">{$lblTitle|ucfirst}</label>
									{$txtTitle} {$txtTitleError}
								</p>
								<label for="content">{$lblContent|ucfirst}</label>
								<p>{$txtContent} {$txtContentError}</p>
								<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label></p>
							</fieldset>
						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}