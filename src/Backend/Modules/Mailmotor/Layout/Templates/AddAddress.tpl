{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblEmailAddresses|ucfirst}</h2>
</div>

{form:add}
	<div class="box">
		<div class="heading ">
			<h3>{$lblAddEmail|ucfirst}</h3>
		</div>
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="email">{$lblEmailAddress|ucfirst}</label>
					<span style="float: left;">
						{$txtEmail} {$txtEmailError}
					</span>
				</p>
			</div>
		</div>
		<div class="horizontal">
			<div class="options">
				{option:groups}
					<ul class="inputList">
						{iteration:groups}<li>{$groups.chkGroups} <label for="{$groups.id}">{$groups.label|ucfirst}</label></li>{/iteration:groups}
					</ul>
				{/option:groups}
				{$chkGroupsError}
			</div>
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
