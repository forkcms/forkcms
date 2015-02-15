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

	{* Dialog for a textbox *}
	<div id="textboxDialog" title="{$lblTextbox|ucfirst}" class="dialog" style="display: none;">
		<input type="hidden" name="textbox_id" id="textboxId" value="" />

		<div class="tabs forkForms">
			<ul>
				<li><a href="#tabTextboxBasic">{$lblBasic|ucfirst}</a></li>
				<li><a href="#tabTextboxProperties">{$lblProperties|ucfirst}</a></li>
				<li><a href="#tabTextboxAdvanced">{$lblAdvanced|ucfirst}</a></li>
			</ul>

			<div id="tabTextboxBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textboxLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextboxLabel}
							<span id="textboxLabelError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>
			<div id="tabTextboxProperties" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textboxValue">{$lblDefaultValue|ucfirst}</label>
							{$txtTextboxValue}
						</p>
					</div>
					<div class="options">
						<p class="p0">
							<label for="textboxReplyTo">{$lblReplyTo|ucfirst}</label>
							{$chkTextboxReplyTo}
							<abbr class="help">(?)</abbr>
							<span class="tooltip" style="display: none;">{$msgHelpReplyTo}</span>
						</p>
					</div>
					<div class="validation options">
						<p class="p0">
							<label for="textboxRequired">{$lblRequiredField|ucfirst}</label>
							{$chkTextboxRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="textboxRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextboxRequiredErrorMessage}
							<span id="textboxRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
					<div class="validation options">
						<p class="p0">
							<label for="textboxValidation">{$lblValidation|ucfirst}</label>
							{$ddmTextboxValidation}
						</p>
						<span id="textboxReplyToErrorMessageError" class="formError" style="display: none;"></span>
						<p class="validationParameter" style="display: none;">
							<label for="textboxValidationParameter">{$lblParameter|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextboxValidationParameter}
						</p>
						<p class="validationErrorMessage" style="display: none;">
							<label for="textboxErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextboxErrorMessage}
							<span id="textboxErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>

			</div>

			<div id="tabTextboxAdvanced" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textareaPlaceholder">{$lblPlaceholder|ucfirst}</label>
							{$txtTextboxPlaceholder}
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{* Dialog for a textarea *}
	<div id="textareaDialog" title="{$lblTextarea|ucfirst}" class="dialog" style="display: none;">
		<input type="hidden" name="textarea_id" id="textareaId" value="" />

		<div class="tabs forkForms">
			<ul>
				<li><a href="#tabTextareaBasic">{$lblBasic|ucfirst}</a></li>
				<li><a href="#tabTextareaProperties">{$lblProperties|ucfirst}</a></li>
				<li><a href="#tabTextareaAdvanced">{$lblAdvanced|ucfirst}</a></li>
			</ul>

			<div id="tabTextareaBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textareaLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextareaLabel}
							<span id="textareaLabelError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>

			<div id="tabTextareaProperties" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textareaValue">{$lblDefaultValue|ucfirst}</label>
							{$txtTextareaValue}
						</p>
					</div>
					<div class="validation options">
						<p class="p0">
							<label for="textareaRequired">{$lblRequiredField|ucfirst}</label>
							{$chkTextareaRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="textareaRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextareaRequiredErrorMessage}
							<span id="textareaRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
					<div class="validation options" style="display: none;">
						<p class="p0">
							<label for="textareaValidation">{$lblValidation|ucfirst}</label>
							{$ddmTextareaValidation}
						</p>
						<p class="validationParameter" style="display: none;">
							<label for="textareaValidationParameter">{$lblParameter|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextareaValidationParameter}
						</p>
						<p class="validationErrorMessage" style="display: none;">
							<label for="textareaErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtTextareaErrorMessage}
							<span id="textareaErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>

			<div id="tabTextareaAdvanced" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textareaPlaceholder">{$lblPlaceholder|ucfirst}</label>
							{$txtTextareaPlaceholder}
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{* Dialog for a datetime *}
	<div id="datetimeDialog" title="{$lblDatetime|ucfirst}" class="dialog" style="display: none;">
		<input type="hidden" name="datetime_id" id="datetimeId" value="" />

		<div class="tabs forkForms">
			<ul>
				<li><a href="#tabDatetimeBasic">{$lblBasic|ucfirst}</a></li>
				<li><a href="#tabDatetimeProperties">{$lblProperties|ucfirst}</a></li>
			</ul>

			<div id="tabDatetimeBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="datetimeLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtDatetimeLabel}
							<span id="datetimeLabelError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>
			<div id="tabDatetimeProperties" class="box">
				<div class="horizontal">
					<div class="defaultValue options">
						<p>
							<label for="datetimeValue">{$lblDefaultValue|ucfirst}</label>
							{$ddmDatetimeValueAmount} {$ddmDatetimeValueType}
							<span id="datetimeDefaultValueErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
					<div class="validation options">
						<p class="p0">
							<label for="datetimeRequired">{$lblRequiredField|ucfirst}</label>
							{$chkDatetimeRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="datetimeRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtDatetimeRequiredErrorMessage}
							<span id="datetimeRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
					<div class="type options">
						<p class="p0">
							<label for="datetimeType">{$lblType|ucfirst}</label>
							{$ddmDatetimeType}
						</p>
						<p class="typeParameter" style="display: none;">
							<label for="datetimeTypeParameter">{$lblParameter|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtDatetimeTypeParameter}
						</p>
					</div>
					<div class="validation options">
						<p class="p0">
							<label for="datetimeValidation">{$lblValidation|ucfirst}</label>
							{$ddmDatetimeValidation}
						</p>
						<p class="validationErrorMessage" style="display: none;">
							<label for="datetimeErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtDatetimeErrorMessage}
							<span id="datetimeErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>

			</div>
		</div>
	</div>

	{* Dialog for a dropdown *}
	<div id="dropdownDialog" title="{$lblDropdown|ucfirst}" class="dialog" style="display: none;">
		<input type="hidden" name="dropdown_id" id="dropdownId" value="" />

		<div class="tabs forkForms">
			<ul>
				<li><a href="#tabDropdownBasic">{$lblBasic|ucfirst}</a></li>
				<li><a href="#tabDropdownProperties">{$lblProperties|ucfirst}</a></li>
			</ul>

			<div id="tabDropdownBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="dropdownLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtDropdownLabel}
							<span id="dropdownLabelError" class="formError" style="display: none;"></span>
						</p>
						<p>
							<label for="dropdownValues">{$lblValues|ucfirst}</label>
							{$txtDropdownValues} {$txtDropdownValuesError}
							<span id="dropdownValuesError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>

			<div id="tabDropdownProperties" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="dropdownDefaultValue">{$lblDefaultValue|ucfirst}</label>
							{$ddmDropdownDefaultValue}
						</p>
					</div>
					<div class="options validation">
						<p class="p0">
							<label for="dropdownRequired">{$lblRequiredField|ucfirst}</label>
							{$chkDropdownRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="dropdownRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtDropdownRequiredErrorMessage}
							<span id="dropdownRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{* Dialog for a radiobutton *}
	<div id="radiobuttonDialog" title="{$lblRadiobutton|ucfirst}" class="dialog" style="display: none;">
		<input type="hidden" name="radiobutton_id" id="radiobuttonId" value="" />

		<div class="tabs forkForms">
			<ul>
				<li><a href="#tabRadiobuttonBasic">{$lblBasic|ucfirst}</a></li>
				<li><a href="#tabRadiobuttonProperties">{$lblProperties|ucfirst}</a></li>
			</ul>

			<div id="tabRadiobuttonBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="radiobuttonLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtRadiobuttonLabel}
							<span id="radiobuttonLabelError" class="formError" style="display: none;"></span>
						</p>
						<p>
							<label for="radiobuttonValues">{$lblValues|ucfirst}</label>
							{$txtRadiobuttonValues} {$txtRadiobuttonValuesError}
							<span id="radiobuttonValuesError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>

			<div id="tabRadiobuttonProperties" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="radiobuttonDefaultValue">{$lblDefaultValue|ucfirst}</label>
							{$ddmRadiobuttonDefaultValue}
						</p>
					</div>
					<div class="options validation">
						<p class="p0">
							<label for="radiobuttonRequired">{$lblRequiredField|ucfirst}</label>
							{$chkRadiobuttonRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="radiobuttonRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtRadiobuttonRequiredErrorMessage}
							<span id="radiobuttonRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{* Dialog for a checkbox *}
	<div id="checkboxDialog" title="{$lblCheckbox|ucfirst}" class="dialog" style="display: none;">
		<input type="hidden" name="checkbox_id" id="checkboxId" value="" />

		<div class="tabs forkForms">
			<ul>
				<li><a href="#tabCheckboxBasic">{$lblBasic|ucfirst}</a></li>
				<li><a href="#tabCheckboxProperties">{$lblProperties|ucfirst}</a></li>
			</ul>

			<div id="tabCheckboxBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="checkboxLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtCheckboxLabel}
							<span id="checkboxLabelError" class="formError" style="display: none;"></span>
						</p>
						<p>
							<label for="checkboxValues">{$lblValues|ucfirst}</label>
							{$txtCheckboxValues} {$txtCheckboxValuesError}
						</p>
					</div>
				</div>
			</div>

			<div id="tabCheckboxProperties" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="checkboxDefaultValue">{$lblDefaultValue|ucfirst}</label>
							{$ddmCheckboxDefaultValue}
						</p>
					</div>
					<div class="options validation">
						<p class="p0">
							<label for="checkboxRequired">{$lblRequiredField|ucfirst}</label>
							{$chkCheckboxRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="checkboxRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
							{$txtCheckboxRequiredErrorMessage}
							<span id="checkboxRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{* Dialog for the submit button *}
	<div id="submitDialog" title="{$lblSubmitButton|ucfirst}" class="dialog box forkForms" style="display: none;">
		<div class="horizontal">
			<div class="options">
				<input type="hidden" name="submit_id" id="submitId" value="" />
				<p>
					<label for="submit">{$lblContent|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtSubmit}
				</p>
				<div class="validation">
					<span id="submitError" class="formError" style="display: none;"></span>
				</div>
			</div>
		</div>
	</div>

	{* Dialog for a heading *}
	<div id="headingDialog" title="{$lblHeading|ucfirst}" class="dialog box forkForms" style="display: none;">
		<div class="horizontal">
			<div class="options">
				<input type="hidden" name="heading_id" id="headingId" value="" />
				<p>
					<label for="heading">{$lblContent|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtHeading}
					<span id="headingError" class="formError" style="display: none;"></span>
				</p>
			</div>
		</div>
	</div>

	{* Dialog for a paragraph *}
	<div id="paragraphDialog" title="{$lblParagraph|ucfirst}" class="dialog box boxLevel2 forkForms" style="display: none;">
		<input type="hidden" name="paragraph_id" id="paragraphId" value="" />
		<div class="heading">
			<h3>{$lblContent|ucfirst}</h3>
		</div>
		<p>
			{$txtParagraph}
			<span id="paragraphError" class="formError" style="display: none;"></span>
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
