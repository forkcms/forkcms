{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
