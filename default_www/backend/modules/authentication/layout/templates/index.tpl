{include:file="{$BACKEND_CORE_PATH}/layout/templates/head.tpl"}
<body>
	{option:debug}<div id="debugnotify">Debug mode</div>{/option:debug}
	<table border="0" cellspacing="0" cellpadding="0" id="loginHolder">
		<tr>
			<td>
				<div id="loginNav">
					<ul>
						<li><span>Fork</span> <strong>CMS</strong></li>
						<li><a href="http://userguide.fork-cms.be">{$lblUserguide|ucfirst}</a></li>
						<li><a href="http://docs.fork-cms.be">{$lblDeveloper|ucfirst}</a></li>
					</ul>
				</div>
				<div id="loginBox">
					<div id="loginBoxTop">
						<h2>{$SITE_TITLE}</h2>
						<p>{$msgLoginFormHelp}</p>
					</div>
					
					{form:authenticationIndex}
						<div class="horizontal">
							{option:hasError}
							<div class="errorMessage singleMessage">
								<p>{$errInvalidUsernamePasswordCombination}</p>
							</div>{/option:hasError}
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
								{$btnLogin}
							</p>
						</div>
					{/form:authenticationIndex}
					<a href="#" id="forgotPasswordLink" class="toggleBalloon" rel="forgotPasswordHolder">{$msgForgotPassword}</a>
				</div>
				<div id="forgotPasswordHolder" class="balloon"{option:!showForm} style="display: none;"{/option:!showForm}>
					<div id="forgotPassword">
						<div class="balloonTop">&nbsp;</div>
						<p>{$msgForgotPasswordHelp}</p>
						{form:forgotPassword}
							<div class="oneLiner">
								<p><label for="backendEmail">{$lblEmail}</label></p>
								<p>{$txtBackendEmail}</p>
								<p>{$btnSend}{$txtBackendEmailError}</p>
							</div>
							
							{option:hasForgotpasswordError}
							<div class="errorMessage singleMessage">
								<p>{$msgLoginFormForgotPasswordError}</p>
							</div>
							{/option:hasForgotpasswordError}
							
							{option:isForgotpasswordSuccess}
							<div class="successMessage singleMessage">
								<p>{$msgLoginFormForgotPasswordSuccess}</p>
							</div>
							{/option:isForgotpasswordSuccess}
						{/form:forgotPassword}
					</div>
				</div>
			</td>
		</tr>
	</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}