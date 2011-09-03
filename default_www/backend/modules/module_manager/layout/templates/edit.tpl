{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblModuleManager|ucfirst}: {$lblEdit}</h3>
		</div>
		
		<div class="options">
			<p>
				<label for="name">{$lblName|ucfirst}</label>
				{$txtName} {$txtNameError}
			</p>
			<p>
				<label for="description">{$lblDescription|ucfirst}</label>
				{$txtDescription} {$txtDescriptionError}
			</p>
			
			<div class="spacing">
				<ul class="inputList pb0">
					<li><label for="active">{$chkActive} {$lblActive|ucfirst}</label> {$chkActiveError}</li>
				</ul>
			</div>
			
		</div>
	</div>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete'}&amp;module={$item.name}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
			
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>
	
	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.name}}
		</p>
	</div>
	
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
