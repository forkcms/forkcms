{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
	<div class="col-md-12">
		<h2>{$lblEditForm|sprintf:{$name}|ucfirst}</h2>
	</div>
</div>
{form:edit}
	<script type="text/javascript">
		//@todo why data comes not from action?
		//<![CDATA[
			var defaultErrorMessages = {};
			{option:errors}
			{iteration:errors}
			defaultErrorMessages.{$errors.type} = '{$errors.message}';
			{/iteration:errors}
			{/option:errors}
		//]]>
	</script>
	<input type="hidden" name="id" id="formId" value="{$id}" />
	<div class="row fork-module-content">
		<div class="col-md-12">
			<div role="tabpanel">
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active">
						<a href="#tabGeneral" aria-controls="general" role="tab" data-toggle="tab">{$lblGeneral|ucfirst}</a>
					</li>
					<li role="presentation">
						<a href="#tabFields" aria-controls="fields" role="tab" data-toggle="tab">{$lblFields|ucfirst}</a>
					</li>
					<li role="presentation">
						<a href="#tabExtra" aria-controls="extra" role="tab" data-toggle="tab">{$lblExtra|ucfirst}</a>
					</li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="tabGeneral">
						<div class="row">
							<div class="col-md-12">
								<h3>{$lblGeneral|ucfirst}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="name">
										{$lblName|ucfirst}
										<abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
									</label>
									{$txtName} {$txtNameError}
								</div>
								<div class="form-group">
									<label for="method">
										{$lblMethod|ucfirst}
										<abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
									</label>
									{$ddmMethod} {$ddmMethodError}
								</div>
								<div class="form-group">
									<label for="email">
										{$lblRecipient|ucfirst}
										<abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
									</label>
									{$txtEmail} {$txtEmailError}
								</div>
								<div class="form-group">
									<label for="successMessage">
										{$lblSuccessMessage|ucfirst}
										<abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
									</label>
									{$txtSuccessMessage} {$txtSuccessMessageError}
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="tabFields">
						<div class="row">
							<div class="col-md-12">
								<h3>{$lblFields|ucfirst}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="generalMessage infoMessage singleMessage content">
									<p class="lastChild">{$msgImportantImmediateUpdate}</p>
								</div>
								<div class="clearfix">
									<div id="leftColumn">
										<div class="box boxLevel2">
											<div class="heading">
												<h3>{$lblPreview|ucfirst}</h3>
											</div>
											<div id="fieldsHolder" class="sequenceByDragAndDrop">
												{option:fields}
												{iteration:fields}
												{$fields.field}
												{/iteration:fields}
												{/option:fields}

												{* This row always needs to be here. We show/hide it with javascript *}
												<div id="noFields" class="options"{option:fields} style="display: none;"{/option:fields}>
													<img src="/src/Backend/Modules/FormBuilder/Layout/images/placeholder_{$INTERFACE_LANGUAGE}.png" alt="{$msgNoFields}" />
												</div>

												{* Submit button is always here. Cannot be deleted or moved. *}
												<div class="options clearfix">
													<p class="floatLeft buttonHolder p0">
														{$btnSubmitField}
													</p>
													<p class="floatRight buttonHolderRight p0">
														<a href="#edit-{$submitId}" class="button iconOnly icon iconEdit editField floatRight" rel="{$submitId}"><span>{$lblEdit}</span></a>
													</p>
												</div>
											</div>
										</div>
									</div>
									<div id="rightColumn">
										<div class="box boxLevel2" id="formElements">
											<div class="heading">
												<h3>{$lblAddFields|ucfirst}</h3>
											</div>
											<div class="options">
												<h3>{$lblFormElements|ucfirst}</h3>
												<ul>
													<li id="textboxSelector"><a href="#textbox" rel="textboxDialog" class="jsFieldDialogTrigger">{$lblTextbox|ucfirst}</a></li>
													<li id="textareaSelector"><a href="#textarea" rel="textareaDialog" class="jsFieldDialogTrigger">{$lblTextarea|ucfirst}</a></li>
													<li id="dropdownSelector"><a href="#dropdown" rel="dropdownDialog" class="jsFieldDialogTrigger">{$lblDropdown|ucfirst}</a></li>
													<li id="checkboxSelector"><a href="#checkbox" rel="checkboxDialog" class="jsFieldDialogTrigger">{$lblCheckbox|ucfirst}</a></li>
													<li id="radiobuttonSelector"><a href="#radiobutton" rel="radiobuttonDialog" class="jsFieldDialogTrigger">{$lblRadiobutton|ucfirst}</a></li>
												</ul>
											</div>
											<div class="options">
												<h3>{$lblTextElements|ucfirst}</h3>
												<ul>
													<li id="headingSelector"><a href="#heading" rel="headingDialog" class="jsFieldDialogTrigger">{$lblHeading|ucfirst}</a></li>
													<li id="paragraphSelector"><a href="#paragraph" rel="paragraphDialog" class="jsFieldDialogTrigger">{$lblParagraph|ucfirst}</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="tabExtra">
						<div class="row">
							<div class="col-md-12">
								<h3>{$lblExtra|ucfirst}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label for="identifier">
									{$lblIdentifier|ucfirst}
									<abbr class="glyphicon glyphicon-info-sign" title="{$msgHelpIdentifier}"></abbr>
								</label>
								{$txtIdentifier} {$txtIdentifierError}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row fork-page-actions">
		<div class="col-md-12">
			<div class="btn-toolbar">
				<div class="btn-group pull-left" role="group">
					{option:showPagesDelete}
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
							<span class="glyphicon glyphicon-trash"></span>
							{$lblDelete|ucfirst}
						</button>
					{/option:showPagesDelete}
				</div>
				<div class="btn-group pull-right" role="group">
					<a href="#" id="saveAsDraft" class="btn btn-primary">
						<span class="glyphicon glyphicon-save"></span>&nbsp;
						{$lblSaveDraft|ucfirst}
					</a>
					<button id="editButton" type="submit" name="edit" class="btn btn-primary">
						<span class="glyphicon glyphicon-pencil"></span>&nbsp;
						{$lblSave|ucfirst}
					</button>
				</div>
			</div>
		</div>
	</div>
	{include:{$BACKEND_MODULES_PATH}/FormBuilder/Layout/Templates/Dialogs.tpl}

	<div class="fullwidthOptions">

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>


{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
