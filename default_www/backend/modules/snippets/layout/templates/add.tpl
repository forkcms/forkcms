{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
	<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblSnippets|ucfirst} &gt; {$lblAdd|ucfirst}</p>
			</div>

			{option:formError}
			<div id="report">
				<div class="singleMessage errorMessage">
					<p>{$errFormError}</p>
				</div>
			</div>
			{/option:formError}

			<div class="inner">
				{form:add}
					<p>
						<label for="title">{$lblTitle|ucfirst}</label>
						{$txtTitle} {$txtTitleError}
					</p>

					<div class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							<fieldset>
								<label for="content">{$lblContent|ucfirst}</label>
								<p>{$txtContent} {$txtContentError}</p>
								<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label></p>
							</fieldset>
						</div>
					</div>
					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="add" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}







