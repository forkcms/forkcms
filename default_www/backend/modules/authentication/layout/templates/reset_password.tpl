{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
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
						<p>{$msgResetPasswordFormHelp}</p>
					</div>

					{form:authenticationResetPassword}
						<div class="horizontal">
							{option:error}
							<div class="errorMessage singleMessage">
								<p>{$error}</p>
							</div>{/option:error}
							<div id="loginFields">
								<p>
									<label for="backendPassword">{$lblNewPassword|ucfirst}</label>
									{$txtBackendNewPassword} {$txtBackendNewPasswordError}
								</p>

								<p>
									<label for="backendPasswordRepeated">{$lblRepeatPassword|ucfirst}</label>
									{$txtBackendNewPasswordRepeated} {$txtBackendNewPasswordRepeatedError}
								</p>
							</div>
							<p class="spacing">
								{$btnLogin}
							</p>
						</div>
					{/form:authenticationResetPassword}
				</div>
			</td>
		</tr>
	</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}