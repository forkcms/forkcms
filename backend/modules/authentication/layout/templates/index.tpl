{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
<body id="login">
	{include:{$BACKEND_MODULES_PATH}/{$MODULE}/layout/templates/ie6.tpl}
	{option:debug}<div id="debugnotify">Debug mode</div>{/option:debug}
	<table id="loginHolder">
		<tr>
			<td>
				{option:hasError}
					<div id="loginError">
						<div class="errorMessage singleMessage">
							<p>{$errInvalidEmailPasswordCombination}</p>
						</div>
					</div>
				{/option:hasError}

				{option:hasTooManyAttemps}
					<div id="loginError">
						<div class="errorMessage singleMessage">
							<p>{$errTooManyLoginAttempts}</p>
						</div>
					</div>
				{/option:hasTooManyAttemps}

				<div id="loginBox" {option:hasError}class="hasError"{/option:hasError}>
					<div id="loginBoxTop">
						<h2>{$SITE_TITLE}</h2>
					</div>

					{form:authenticationIndex}
						<div class="horizontal">
							<div id="loginFields">
								<p>
									<label for="backendEmail">{$lblEmail|ucfirst}</label>
									{$txtBackendEmail} {$txtBackendEmailError}
								</p>
								<p>
									<label for="backendPassword">{$lblPassword|ucfirst}</label>
									{$txtBackendPassword} {$txtBackendPasswordError}
								</p>
							</div>
							<p class="spacing">
								<input name="login" type="submit" value="{$lblSignIn|ucfirst}" class="inputButton button mainButton" />
							</p>
						</div>
					{/form:authenticationIndex}

					<ul id="loginNav">
						<li><a href="#" id="forgotPasswordLink" class="toggleBalloon" data-message-id="forgotPasswordHolder">{$msgForgotPassword}</a></li>
					</ul>
				</div>

				<div id="forgotPasswordHolder" class="balloon {option:!showForm}balloonNoMessage{/option:!showForm}"{option:!showForm} style="display: none;"{/option:!showForm}>
					<div id="forgotPasswordBox">

						<a class="button linkButton icon iconClose iconOnly toggleBalloon" href="#" data-message-id="forgotPasswordHolder"><span>X</span></a>

						<div class="balloonTop">&nbsp;</div>

						<p>{$msgHelpForgotPassword}</p>
						{form:forgotPassword}
							<div class="oneLiner">
								<p><label for="backendEmailForgot">{$lblEmail|ucfirst}</label></p>
								<p>{$txtBackendEmailForgot}</p>
								<p>
									<input id="send" type="submit" name="send" value="{$lblSend|ucfirst}" />
								</p>
							</div>

							{option:txtBackendEmailForgotError}
								<div class="errorMessage singleMessage">
									<p>{$txtBackendEmailForgotError}</p>
								</div>
							{/option:txtBackendEmailForgotError}

							{option:isForgotPasswordSuccess}
								<div class="successMessage singleMessage">
									<p>{$msgLoginFormForgotPasswordSuccess}</p>
								</div>
							{/option:isForgotPasswordSuccess}
						{/form:forgotPassword}
					</div>
				</div>

			</td>
		</tr>
	</table>
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}