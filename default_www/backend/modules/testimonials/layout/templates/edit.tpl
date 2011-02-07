{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblTestimonials|ucfirst}: {$msgEdit|sprintf:{$item['name']}}</h2>
</div>

{form:edit}
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td id="leftColumn">

				<div class="content">
					<div class="options">
						<p>
							<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtName} {$txtNameError}
						</p>
					</div>
				</div>

				<div class="box">
					<div class="heading">
						<h3>{$lblTestimonial|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
					</div>
					<div class="optionsRTE">
						{$txtTestimonial} {$txtTestimonialError}
					</div>
				</div>

			</td>

			<td id="sidebar">

				<div id="publishOptions" class="box">
					<div class="heading">
						<h3>{$lblStatus|ucfirst}</h3>
					</div>

					<div class="options">
						<ul class="inputList">
							{iteration:hidden}
							<li>
								{$hidden.rbtHidden}
								<label for="{$hidden.id}">{$hidden.label}</label>
							</li>
							{/iteration:hidden}
						</ul>
					</div>
				</div>

			</td>
		</tr>
	</table>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete'}&amp;id={$item['id']}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblPublish|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item['name']}}
		</p>
	</div>
{/form:edit}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}