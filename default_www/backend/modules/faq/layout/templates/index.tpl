{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblFaq|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

<div id="datagridQuestionsHolder">
	{option:datagrids}
		{iteration:datagrids}
			<div class="datagridHolder" id="datagrid-{$datagrids.id}">
				<div class="tableHeading">
					<h3>{$datagrids.name}</h3>
				</div>
				{option:datagrids.content}
					{$datagrids.content}
				{/option:datagrids.content}

				{option:!datagrids.content}
					<table class="datagrid sequenceByDragAndDrop" cellspacing="0" cellpadding="0" border="0">
						<thead>
							<tr>
								<th class="dragAndDropHandle">
									<span>&#160;</span>
								</th>
								<th class="question">
									<span>{$lblQuestion|ucfirst}</span>
								</th>
								<th class="hidden">
									<span>{$lblHidden|ucfirst}</span>
								</th>
								<th class="edit">
									<span>&#160;</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr class="noQuestions">
								<td colspan="3">{$msgNoQuestionInCategory}</td>
							</tr>
						</tbody>
					</table>
				{/option:!datagrids.content}
			</div>
		{/iteration:datagrids}
	{/option:datagrids}
</div>

{option:!datagrids}
	<p>{$msgNoItems}</p>
{/option:!datagrids}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}