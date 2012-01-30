{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="pageTitle">
		<h2>{$lblFormBuilder|ucfirst}: {$lblEditForm|sprintf:{$name}}</h2>
	</div>

	<script type="text/javascript">
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

	<div class="tabs">
		<ul>
			<li><a href="#tabGeneral">{$lblGeneral|ucfirst}</a></li>
			<li><a href="#tabFields">{$lblFields|ucfirst}</a></li>
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
						<label for="addValue-email">{$lblRecipient|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
			</div>
		</div>

		<div id="tabFields">
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
								<img src="/backend/modules/form_builder/layout/images/placeholder_{$INTERFACE_LANGUAGE}.png" alt="{$msgNoFields}" />
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
								<li id="textboxSelector"><a href="#textbox" rel="textboxDialog" class="openFieldDialog">{$lblTextbox|ucfirst}</a></li>
								<li id="textareaSelector"><a href="#textarea" rel="textareaDialog" class="openFieldDialog">{$lblTextarea|ucfirst}</a></li>
								<li id="dropdownSelector"><a href="#dropdown" rel="dropdownDialog" class="openFieldDialog">{$lblDropdown|ucfirst}</a></li>
								<li id="checkboxSelector"><a href="#checkbox" rel="checkboxDialog" class="openFieldDialog">{$lblCheckbox|ucfirst}</a></li>
								<li id="radiobuttonSelector"><a href="#radiobutton" rel="radiobuttonDialog" class="openFieldDialog">{$lblRadiobutton|ucfirst}</a></li>
							</ul>
						</div>
						<div class="options">
							<h3>{$lblTextElements|ucfirst}</h3>
							<ul>
								<li id="headingSelector"><a href="#heading" rel="headingDialog" class="openFieldDialog">{$lblHeading|ucfirst}</a></li>
								<li id="paragraphSelector"><a href="#paragraph" rel="paragraphDialog" class="openFieldDialog">{$lblParagraph|ucfirst}</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

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
		{option:showFormBuilderDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>{$msgConfirmDelete|sprintf:{$name}}</p>
		</div>
		{/option:showFormBuilderDelete}

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
			</ul>

			<div id="tabTextboxBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textboxLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
					<div class="validation options">
						<p class="p0">
							<label for="textboxRequired">{$lblRequiredField|ucfirst}</label>
							{$chkTextboxRequired}
						</p>
						<p class="validationRequiredErrorMessage hidden">
							<label for="textboxRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTextboxRequiredErrorMessage}
							<span id="textboxRequiredErrorMessageError" class="formError" style="display: none;"></span>
						</p>
					</div>
					<div class="validation options">
						<p class="p0">
							<label for="textboxValidation">{$lblValidation|ucfirst}</label>
							{$ddmTextboxValidation}
						</p>
						<p class="validationParameter" style="display: none;">
							<label for="textboxValidationParameter">{$lblParameter|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTextboxValidationParameter}
						</p>
						<p class="validationErrorMessage" style="display: none;">
							<label for="textboxErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTextboxErrorMessage}
							<span id="textboxErrorMessageError" class="formError" style="display: none;"></span>
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
			</ul>

			<div id="tabTextareaBasic" class="box">
				<div class="horizontal">
					<div class="options">
						<p>
							<label for="textareaLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="textareaRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="textareaValidationParameter">{$lblParameter|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTextareaValidationParameter}
						</p>
						<p class="validationErrorMessage" style="display: none;">
							<label for="textareaErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTextareaErrorMessage}
							<span id="textareaErrorMessageError" class="formError" style="display: none;"></span>
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
							<label for="dropdownLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="dropdownRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="radiobuttonLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="radiobuttonRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="checkboxLabel">{$lblLabel|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
							<label for="checkboxRequiredErrorMessage">{$lblErrorMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
					<label for="submit">{$lblContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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
					<label for="heading">{$lblContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
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

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}