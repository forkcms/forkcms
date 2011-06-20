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

<div id="dataGridQuestionsHolder">
	{option:dataGrids}
		{iteration:dataGrids}
			<div class="dataGridHolder" id="dataGrid-{$dataGrids.id}">
				<div class="tableHeading">
					<h3>{$dataGrids.name}</h3>
				</div>
				{option:dataGrids.content}
					{$dataGrids.content}
				{/option:dataGrids.content}

				{option:!dataGrids.content}
					<table class="dataGrid sequenceByDragAndDrop" cellspacing="0" cellpadding="0" border="0">
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
				{/option:!dataGrids.content}
			</div>
		{/iteration:dataGrids}
	{/option:dataGrids}
</div>

{option:!dataGrids}
	<p>{$msgNoItems}</p>
{/option:!dataGrids}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}