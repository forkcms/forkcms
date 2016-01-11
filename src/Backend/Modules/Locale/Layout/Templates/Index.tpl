{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblTranslations|ucfirst}</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showLocaleAdd}
        <a href="{$var|geturl:'add'}{$filter}" class="btn btn-default jsButtonAdd">
          <span class="glyphicon glyphicon-plus"></span>&nbsp;
          <span>{$lblAdd|ucfirst}</span>
        </a>
        {/option:showLocaleAdd}
        {option:showLocaleExport}
        <a href="{$var|geturl:'export'}{$filter}" class="btn btn-default jsButtonExport">
          <span class="glyphicon glyphicon-export"></span>&nbsp;
          <span>{$lblExport|ucfirst}</span>
        </a>
        {/option:showLocaleExport}
        {option:showLocaleImport}
        <a href="{$var|geturl:'import'}{$filter}" class="btn btn-default jsButtonImport">
          <span class="glyphicon glyphicon-import"></span>&nbsp;
          <span>{$lblImport|ucfirst}</span>
        </a>
        {/option:showLocaleImport}
      </div>
    </div>
  </div>
</div>

<div class="dataGridHolder">
	{form:filter}
		<div class="dataFilter">
			<table>
				<tbody>
					<tr>
						<td>
							<div class="options">
								<p>
									<label for="application">{$lblApplication|ucfirst}</label>
									{$ddmApplication} {$ddmApplicationError}
								</p>
								<p>
									<label for="module">{$lblModule|ucfirst}</label>
									{$ddmModule} {$ddmModuleError}
								</p>
							</div>
						</td>
						<td>
							<div class="options">
								<label>{$lblTypes|ucfirst}</label>
								{option:type}
									<ul>
										{iteration:type}<li>{$type.chkType} <label for="{$type.id}">{$type.label|ucfirst}</label></li>{/iteration:type}
									</ul>
								{/option:type}
							</div>
						</td>
						<td>
							<div class="options">
								<label>{$lblLanguages|ucfirst}</label>
								{option:language}
									<ul>
										{iteration:language}<li>{$language.chkLanguage} <label for="{$language.id}">{$language.label|ucfirst}</label></li>{/iteration:language}
									</ul>
								{/option:language}
							</div>
						</td>
						<td>
							<div class="options">
								<div class="oneLiner">
									<p>
										<label for="name">{$lblReferenceCode|ucfirst}</label>
									</p>
									<p>
										<abbr class="help">(?)</abbr>
										<span class="tooltip" style="display: none;">
											{$msgHelpName}
										</span>
									</p>
								</div>
								{$txtName} {$txtNameError}

								<div class="oneLiner">
									<p>
										<label for="value">{$lblValue|ucfirst}</label>
									</p>
									<p>
										<abbr class="help">(?)</abbr>
										<span class="tooltip" style="display: none;">
											{$msgHelpValue}
										</span>
									</p>
								</div>
								{$txtValue} {$txtValueError}

							</div>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="99">
							<div class="options">
								<div class="buttonHolder">
									<input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$lblUpdateFilter|ucfirst}" />
								</div>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	{/form:filter}


	{option:dgLabels}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblLabels|ucfirst}</h3>
		</div>
		{$dgLabels}
	</div>
	{/option:dgLabels}

	{option:dgMessages}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblMessages|ucfirst}</h3>
		</div>
		{$dgMessages}
	</div>
	{/option:dgMessages}

	{option:dgErrors}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblErrors|ucfirst}</h3>
		</div>
		{$dgErrors}
	</div>
	{/option:dgErrors}

	{option:dgActions}
	<div class="dataGridHolder">
		<div class="tableHeading oneLiner">
			<h3>{$lblActions|ucfirst} </h3>
				<abbr class="help">(?)</abbr>
				<span class="tooltip" style="display: none;">
					{$msgHelpActionValue}
				</span>
		</div>
		{$dgActions}
	</div>
	{/option:dgActions}

	{option:hasSubmissions}
	{option:noItems}
		<p>{$msgNoItemsFilter|sprintf:{$addURL}}</p>
	{/option:noItems}
	{/option:hasSubmissions}
	{option:!hasSubmissions}
		<p>{$msgStartSearch}</p>
	{/option:!hasSubmissions}
</div>
{option:dgLabels}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblLabels|ucfirst}
        </h3>
      </div>
      {$dgLabels}
    </div>
  </div>
</div>
{/option:dgLabels}
{option:dgMessages}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblMessages|ucfirst}
        </h3>
      </div>
      {$dgMessages}
    </div>
  </div>
</div>
{/option:dgMessages}
{option:dgErrors}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblErrors|ucfirst}
        </h3>
      </div>
      {$dgErrors}
    </div>
  </div>
</div>
{/option:dgErrors}
{option:dgActions}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblActions|ucfirst}&nbsp;
          <abbr class="glyphicon glyphicon-info-sign" title="{$msgHelpActionValue}"></abbr>
        </h3>
      </div>
      {$dgActions}
    </div>
  </div>
</div>
{/option:dgActions}
{option:noItems}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <p>{$msgNoItemsFilter|sprintf:{$addURL}}</p>
      </div>
    </div>
  </div>
</div>
{/option:noItems}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
