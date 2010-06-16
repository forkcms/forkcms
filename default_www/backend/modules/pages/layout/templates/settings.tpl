{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				<div class="pageTitle">
					<h2>{$lblModuleSettings|ucfirst}: {$lblPages|ucfirst}</h2>
				</div>

				{form:settings}
					<div class="box">
						<div class="heading">
							<h3>{$lblHasMetaNavigation|ucfirst}</h3>
						</div>
						<div class="options">
							<p>{$msgHelpMetaNavigation}:</p>
							<ul class="inputList p0">
								<li>{$chkHasMetaNavigation} <label for="has_meta_navigation">{$msgHasMetaNavigation|ucfirst}</label></li>
							</ul>
						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
						</div>
					</div>
				{/form:settings}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}