{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

{form:add}
	<div class="pageTitle">
		<h2>{$lblFormBuilder|ucfirst}: {$lblAdd}</h2>
	</div>

	<div class="tabs">
		<ul>
			<li><a href="#tabGeneral">{$lblGeneral|ucfirst}</a></li>
			<li><a href="#tabConfirmationMail">{$lblConfirmationMail|ucfirst}</a></li>
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
		</div>

		<div id="tabConfirmationMail" class="box">
			<div class="generalMessage infoMessage singleMessage">
				<p class="lastChild">{$msgConfirmationMail}</p>
			</div>
			<div class="horizontal">
				<p>
					{$chkSendConfirmationMail} <label for="mail_send">{$lblSendConfirmationMail|ucfirst}</label>
				</p>
				<p class="confirmationEmailContainer">
					<label for="subject">
						{$lblConfirmationMailSubject|ucfirst}
					</label>
					{$txtConfirmationMailSubject} {$txtConfirmationMailSubjectError}
				</p>

				<div class="heading confirmationEmailContainer" >
					<h3>
						<label for="mailContent">{$lblConfirmationMailContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					</h3>
				</div>
				<div class="optionsRTE confirmationEmailContainer">
					{$txtConfirmationMailContent} {$txtConfirmationMailContentError}
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
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
