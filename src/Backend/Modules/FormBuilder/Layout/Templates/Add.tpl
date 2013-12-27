{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:add}
	<div class="pageTitle">
		<h2>{$lblFormBuilder|ucfirst}: {$lblAdd}</h2>
	</div>

	<div class="tabs">
		<ul>
			<li><a href="#tabGeneral">{$lblGeneral|ucfirst}</a></li>
			<li><a href="#tabExtra">{$lblExtra|ucfirst}</a></li>
		</ul>

		<div id="tabGeneral" class="box">
			<div class="horizontal">
				<div class="options">
					<p>
						<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtName} {$txtNameError}
					</p>
				</div>
				<div class="options">
					<p class="p0">
						<label for="method">{$lblMethod|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$ddmMethod} {$ddmMethodError}
					</p>
					<p id="emailWrapper" class="hidden">
						<label for="email">{$lblRecipient|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
				</div>
			</div>
			<div class="options">
				<div class="heading">
					<h3>
						<label for="successMessage">{$lblSuccessMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					</h3>
				</div>
				<div class="optionsRTE">
					{$txtSuccessMessage} {$txtSuccessMessageError}
				</div>
			</div>		</div>

		<div id="tabExtra" class="box">
			<div class="horizontal">
				<div class="options">
					<p>
						<label for="identifier">
							{$lblIdentifier|ucfirst}
							<abbr class="help">(?)</abbr>
							<span class="tooltip" style="display: none;">{$msgHelpIdentifier}</span>
						</label>
						{$txtIdentifier} {$txtIdentifierError}
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}