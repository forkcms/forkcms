{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-header">
	<div class="col-md-12">
		<h2>{$lblAdd|ucfirst}</h2>
	</div>
</div>
{form:add}
	<div class="row fork-module-content">
		<div class="col-md-12">
			<div role="tabpanel">
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active">
						<a href="#tabName" aria-controls="name" role="tab" data-toggle="tab">{$lblName|ucfirst}</a>
					</li>
					<li role="presentation">
						<a href="#tabDashboard" aria-controls="dashboard" role="tab" data-toggle="tab">{$lblDashboard|ucfirst}</a>
					</li>
					<li role="presentation">
						<a href="#tabPermissions" aria-controls="permission" role="tab" data-toggle="tab">{$lblPermissions|ucfirst}</a>
					</li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="tabName">
						<div class="row">
							<div class="col-md-12">
								<h3>{$lblName|ucfirst}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="name">{$lblName|ucfirst}&nbsp;<abbr class="glyphicon glyphicon-info-sign" title="{$lblRequiredField}"></abbr></label>
									{$txtName} {$txtNameError}
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="tabDashboard">
						<div class="row">
							<div class="col-md-12">
								<h3>{$lblDashboard|ucfirst}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="toggleChecksWidgets">{$lblDisplayWidgets|ucfirst}</label>
									{option:widgets}
									{$widgets}
									{/option:widgets}
									{option:!widgets}
									{$msgNoWidgets|ucfirst}
									{/option:!widgets}
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="tabPermissions">
						<div class="row">
							<div class="col-md-12">
								<h3>{$lblModules|ucfirst}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>{$lblSetPermissions|ucfirst}</label>
									<ul id="moduleList" class="list-unstyled">
										{iteration:permissions}
										<li class="module checkbox">
											{$permissions.chk}
											<a href="#" class="icon iconCollapsed container" title="open">
												<span>
													<label for="{$permissions.id}">{$permissions.label}</label>
												</span>
											</a>
											<div class="hide clearfix">
												{$permissions.actions.dataGrid}
											</div>
										</li>
										{/iteration:permissions}
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="pageButtons" class="row fork-page-actions">
		<div class="col-md-12">
			<div class="btn-toolbar">
				<div class="btn-group pull-right" role="group">
					<button id="addButton" type="submit" name="add" class="btn btn-primary">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;{$lblAdd|ucfirst}
					</button>
				</div>
			</div>
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
