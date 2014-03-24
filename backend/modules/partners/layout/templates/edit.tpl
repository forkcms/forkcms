{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPartner|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
	<p>
		<label for="name">{$lblName|ucfirst}<abbr>*</abbr></label>
		{$txtName} {$txtNameError}
	</p>
	<p>
		<label for="img">{$lblImage|ucfirst}</label>
		{$fileImg} {$fileImgError}
		<img class="av128" src="{$FRONTEND_FILES_URL}/partners/{$item.widget}/source/{$item.img}" alt="{$item.name}" />
	</p>
	<p>
		<label for="url">{$lblWebsite|ucfirst}<abbr>*</abbr></label>
		{$txtUrl} {$txtUrlError}
	</p>
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
			<a href="{$var|geturl:'delete'}&amp;id={$item.id}&amp;widget_id={$widgetId}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>

			<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
				<p>
					{$msgConfirmDelete|sprintf:{$item.name}}
				</p>
			</div>
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}