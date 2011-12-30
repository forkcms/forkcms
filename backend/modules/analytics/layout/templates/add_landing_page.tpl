{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:add}
	<div class="box">
		<div class="heading">
			<h4>{$lblAddLandingPage|ucfirst}</h4>
		</div>
		<div class="options">
			<p class="oneLineWrapper bigInput">
				<label for="pagePath">{$lblURL|ucfirst}</label>
				{$SITE_URL} {$txtPagePath} {$txtPagePathError}
			</p>
			<p>
				{option:ddmPageList}
					<label for="pageList">{$lblOr|ucfirst}</label>
					{$ddmPageList} {$ddmPageListError}
				{/option:ddmPageList}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}