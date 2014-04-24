{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}</h2>
</div>

{form:add}
	<div class="box">
		<div class="heading ">
			<h3>{$lblAddGroup|ucfirst}</h3>
		</div>
		<div class="options">
			<label for="name">{$lblName|ucfirst}</label>
			{$txtName} {$txtNameError}
		</div>
		<div class="options">
			<p class="note">{$msgDefaultGroup|ucfirst}</p>
			<ul class="inputList">
				{iteration:default}
				<li>
					{$default.rbtDefault}
					<label for="{$default.id}">{$default.label}</label>
				</li>
				{/iteration:default}
			</ul>
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
