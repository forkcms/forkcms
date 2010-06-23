{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body id="login">

	{include:file='{$BACKEND_MODULES_PATH}/{$MODULE}/layout/templates/ie6.tpl'}

	{option:debug}<div id="debugnotify">WARNING: This Fork is<br /> in debug mode</div>{/option:debug}

	<table border="0" cellspacing="0" cellpadding="0" id="loginHolder">
		<tr>
			<td>
				{option:hasError}
					<div id="loginError">
						<div class="errorMessage singleMessage">
							<p>{$errInvalidUsernamePasswordCombination}</p>
						</div>
					</div>
				{/option:hasError}

				<div id="loginBox" {option:hasError}class="hasError"{/option:hasError}>
					<div id="loginBoxTop">
						<h2>{$SITE_TITLE}</h2>
					</div>

					{form:authenticationIndex}
						<div class="horizontal">
							<div id="loginFields">
								<p>
									<label for="backendUsername">{$lblUsername|ucfirst}</label>
									{$txtBackendUsername} {$txtBackendUsernameError}
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
						<li><a href="http://userguide.fork-cms.be">{$lblUserguide|ucfirst}</a></li>
						<li><a href="http://docs.fork-cms.be">{$lblDeveloper|ucfirst}</a></li>
						<li><a href="#" id="forgotPasswordLink" class="toggleBalloon" rel="forgotPasswordHolder">{$msgForgotPassword}</a></li>
					</ul>
				</div>
				<div id="forgotPasswordHolder" class="balloon {option:!showForm}balloonNoMessage{/option:!showForm}"{option:!showForm} style="display: none;"{/option:!showForm}>
					<div id="forgotPasswordBox">

						<a class="button linkButton icon iconClose iconOnly toggleBalloon" href="#" rel="forgotPasswordHolder"><span>X</span></a>

						<div class="balloonTop">&nbsp;</div>
						
						<p>{$msgHelpForgotPassword}</p>
						{form:forgotPassword}
							<div class="oneLiner">
								<p><label for="backendEmail">{$lblEmail|ucfirst}</label></p>
								<p>{$txtBackendEmail}</p>
								<p>
									<input id="send" type="submit" name="send" value="{$lblSend|ucfirst}" />
								</p>
							</div>

							{option:txtBackendEmailError}
							<div class="errorMessage singleMessage">
								<p>{$txtBackendEmailError}</p>
							</div>
							{/option:txtBackendEmailError}

							{option:hasForgotpasswordError}
							<div class="errorMessage singleMessage">
								<p>{$msgLoginFormForgotPasswordError}</p>
							</div>
							{/option:hasForgotpasswordError}

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

{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}