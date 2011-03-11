{include:'{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:'{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:add}
	<div class="box">
		<div class="heading">
			<h4>{$lblAddLandingPage|ucfirst}</h4>
		</div>
		<div class="options">
			<p class="oneLineWrapper bigInput">
				<label for="page_path">{$lblURL|ucfirst}</label>
				{$SITE_URL} {$txtPagePath} {$txtPagePathError}
			</p>
			<p>
				{option:ddmPageList}
					<label for="page_list">{$lblOr|ucfirst}</label>
					{$ddmPageList} {$ddmPageListError}
				{/option:ddmPageList}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="add" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:'{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:'{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}